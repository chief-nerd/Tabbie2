<?php

namespace common\components\algorithms;

use common\components\TabAlgorithm;
use common\models\Adjudicator;
use common\models\DrawLine;
use common\models\EnergyConfig;
use common\models\Team;
use common\models\Venue;
use Yii;
use yii\base\Exception;

class StrictWUDCRules extends TabAlgorithm {

	/**
	 * Local optima variables
	 */
	private $uphill_probability = 0.5;
	private $alpha = 0.0005;
	private $determination = 500;

	/**
	 * Function to calculate a draw based on WUDC strict rules
	 *
	 * @param Venue[]        $venues Array of active venues for this tournament and round
	 * @param Team[]         $teams  Array of active Teams
	 * @param Adjudicators[] $adjudicators
	 * @param Panel[]        $preset_panels
	 *
	 * @return array
	 * @throws Exception
	 */
	public function makeDraw($venues, $teams, $adjudicators, $preset_panels = array()) {

		Yii::beginProfile("generateDraw");
		$memory_limit = (ini_get('memory_limit') * 1024 * 1024) * 0.9;
		$active_rooms = (count($teams) / 4);

		Yii::beginProfile("initTeamAllocation");
		/**
		 * Shuffle venues
		 */
		shuffle($venues);

		/**
		 * Sort Adjudicator at Strength
		 */
		$adjudicators = $this->sort_adjudicators($adjudicators);

		/**
		 * First we need to make the brackets for each debate. This means ordering the teams by the number of points.
		 */
		$teams = $this->sort_teams($teams);

		/**
		 * Then, within point brackets, we randomise the teams
		 */
		$teams = $this->randomise_within_points($teams);

		/**
		 * Set Past Position Matrix
		 */
		for ($i = 0; $i < count($teams); $i++) {
			$teams[$i]["positionMatrix"] = Team::getPastPositionMatrix($teams[$i]["id"], $this->tournament_id);
		}

		/**
		 * Generate a first rough draw by running the teams down from top to bottom and allocate them
		 */
		for ($i = 0; $i < $active_rooms; $i++) {
			$line = new DrawLine();

			$line->id = $i;
			$choosen = array_splice($teams, 0, 4);
			shuffle($choosen);
			$line->setTeamsByArray($choosen);

			$line->venue = $venues[$i];
			$this->tournament_id = $venues[$i]["tournament_id"];
			$this->DRAW[] = $line;
		}
		$maxIterations = count($this->DRAW);
		Yii::endProfile("initTeamAllocation");

		/**
		 * Now start improving that initial set
		 * Go through the Draw until you can't make any improvements
		 */
		Yii::beginProfile("optimizeTeamAllocation");
		$stillFoundASwap = true;
		while ($stillFoundASwap) {
			$stillFoundASwap = false; //Assume we are done, prove me wrong

			for ($lineIterator = 0; $lineIterator < $maxIterations; $lineIterator++) {
				for ($teamIterator = 0; $teamIterator < 4; $teamIterator++) {

					$posBadness = Team::getPositionBadness($teamIterator, $this->DRAW[$lineIterator]->teams[$teamIterator]);
					if ($posBadness > 0) { // Not optimal positioning exists here
						if ($this->find_best_team_swap_for($this->DRAW[$lineIterator], $teamIterator)) { //Do we find a swap that makes it better
							$stillFoundASwap = true; //We found a better swap, do the loop again
							break;
						}
					}

				}
				if ($stillFoundASwap)
					break; //Found it already break on!
			}
			if (memory_get_usage() > $memory_limit) {
				$stillFoundASwap = false;
				Yii::$app->session->addFlash("error", "Abort <b>Team</b> optimization due to memory limit: " . memory_get_usage() / 1024 / 1024);
			}

			//If we havn't found a better swap $stillFoundASwap should be false and the loop breaks
		}
		Yii::endProfile("optimizeTeamAllocation");

		Yii::beginProfile("initAdjudicatorAllocation");
		/**
		 * Sort Array by strength
		 */
		$this->DRAW = $this->sort_draw_by_points($this->DRAW);

		/**
		 * Initial allocate the adjudicators
		 */
		$lineID = 0;
		foreach ($adjudicators as $adj) {
			$this->DRAW[$lineID]->addAdjudicator($adj);

			if (isset($this->DRAW[$lineID + 1])) //Is there a next line
				$lineID++;
			else
				$lineID = 0; //Start again at beginning
		}

		/**
		 * Calculate initial energylevel
		 */
		for ($lineIterator = 0; $lineIterator < $maxIterations; $lineIterator++) {
			$this->DRAW[$lineIterator] = $this->calcEnergyLevel($this->DRAW[$lineIterator]);
		}
		Yii::endProfile("initAdjudicatorAllocation");

		/**
		 * Now start improving that initial set
		 * Go through the Draw until you can't make any improvements
		 */
		Yii::beginProfile("optimizeAdjudicatorAllocation");

		$this->optimiseAdjudicatorAllocation();

		Yii::endProfile("optimizeAdjudicatorAllocation");
		/*
		 * We have found the best possible combination
		 * There is no better swap possible now.
		 * Return der DRAW[] and get ready to debate
		 */
		Yii::endProfile("generateDraw");

		return $this->DRAW;
	}

	/**
	 * @param \common\components\DrawLine[]|null $DRAW     DrawArra, if null $this->DRAW will be used
	 * @param integer|null                       $max_runs Amount of runs e.g 20000
	 * @param float                              $temp     Tempreture of the simulated annulation
	 *
	 * @return \common\components\DrawLine[]
	 * @throws \yii\base\Exception
	 */
	public function optimiseAdjudicatorAllocation($max_runs = null, $temp = null) {

		$DRAW = &$this->DRAW;

		if ($temp === null)
			$this->temp = 1.0;
		else
			$this->temp = $temp;

		$maxDrawIterations = count($DRAW);
		if ($maxDrawIterations == 0)
			throw new Exception("No rows in draw");

		if ($max_runs == null || !is_int($max_runs))
			$max_runs = $this->energyConfig["max_iterations"];

		$best_draw = $DRAW;
		$this->best_energy = DrawLine::getDrawEnergy($DRAW);
		$best_moment = 0;

		for ($run = 0; $run < $max_runs; $run++) {
			//Get a random line
			do {
				$chosen_line_a = mt_rand(0, ($maxDrawIterations - 1));
				$chosen_line_b = mt_rand(0, ($maxDrawIterations - 1));

				//Get a random adjudicator
				$chosen_adjpos_a = mt_rand(0, (count($DRAW[$chosen_line_a]->getAdjudicators()) - 1));
				$chosen_adjpos_b = mt_rand(0, (count($DRAW[$chosen_line_b]->getAdjudicators()) - 1));

			} while ($chosen_line_a == $chosen_line_b && $chosen_adjpos_a == $chosen_adjpos_b);

			//Create new lines for future
			$new_line_a = $DRAW[$chosen_line_a];
			$new_line_b = $DRAW[$chosen_line_b];

			//Swap the adjudicators in the new line
			$this->swap_adjudicator($new_line_a, $chosen_adjpos_a, $new_line_b, $chosen_adjpos_b);

			//Calculate new Energy Levels for new lines
			$this->calcEnergyLevel($new_line_a);
			$this->calcEnergyLevel($new_line_b);

			//Get Energy Comparison
			$currentEnergy = $DRAW[$chosen_line_a]->energyLevel + $DRAW[$chosen_line_b]->energyLevel;
			$potentialEnergy = $new_line_a->energyLevel + $new_line_b->energyLevel;
			$energyDiff = $potentialEnergy - $currentEnergy;

			/** I don't know what this is doing .. copied from tabbie1 */

			if ($this->throw_dice($this->probability($energyDiff, $temp))) {
				//Keep it
				$DRAW[$chosen_line_a] = $new_line_a;
				$DRAW[$chosen_line_b] = $new_line_b;
			}

			if ($energyDiff < 0) { //better than prev
				$energy = DrawLine::getDrawEnergy($DRAW);
				if ($energy < $this->best_energy) {
					$best_draw = $DRAW;
					$this->best_energy = $energy;
					$best_moment = $run;
				}
			}
			elseif ($run - $best_moment > $this->determination) {
				$best_moment = $run;
				$DRAW = $best_draw;
			}
			$this->temp = $this->decrease_temp($this->temp);
		}
		return $best_draw;
	}

	/**
	 * ???
	 * @param integer $diff
	 * @param float   $temp
	 *
	 * @return int
	 */
	private function probability($diff, $temp) {
		if ($diff <= 0)
			return 1;
		if ($diff > 0)
			return $temp * $this->uphill_probability;
	}

	/**
	 * ????
	 * @param float $probability
	 *
	 * @return bool
	 */
	private function throw_dice($probability) {
		$nr = 10000;
		$dice = mt_rand(0, $nr - 1);
		return ($dice <= $probability * $nr);
	}

	/**
	 * Decreases Temperatur by alpha factor
	 *
	 * @param float $temp
	 *
	 * @return mixed
	 */
	private function decrease_temp($temp) {
		return $temp * (1 - $this->alpha);
	}

	/**
	 * Sort Teams
	 *
	 * @param Team[] $teams
	 *
	 * @return Team[]
	 */
	public
	function sort_teams($teams) {
		//Surpress an error due to a php bug.
		@usort($teams, array('common\models\Team', 'compare_points'));
		return $teams;
	}

	/**
	 * Sort Adjudicator
	 *
	 * @param Adjudicator[] $adj
	 *
	 * @return Adjudicator[]
	 */
	public
	function sort_adjudicators($adj) {
		usort($adj, array('common\models\Adjudicator', 'compare_strength'));
		return $adj;
	}

	/**
	 * Sorts the Draw so strongest room is on top
	 *
	 * @param DrawLine[] $draw
	 *
	 * @return DrawLine[]
	 */
	public
	function sort_draw_by_points($draw) {
		usort($draw, ['common\models\DrawLine', "compare_points"]);
		return $draw;
	}

	/**
	 * Randomises the Teams within Team point brackets
	 *
	 * @param Team[] $teams
	 *
	 * @return Team[]
	 */
	public
	function randomise_within_points($teams) {

		$saved_points = $teams[0]["points"]; //reset to start
		$last_break = 0;

		for ($i = 0; $i < count($teams); $i++) {
			$team_points = $teams[$i]["points"];
			if ($team_points != $saved_points) {
				$bracket = array_slice($teams, $last_break, ($i - $last_break));
				shuffle($bracket);
				array_splice($teams, $last_break, ($i - $last_break), $bracket);

				$last_break = $i;
				$saved_points = $team_points;
			}
		}
		return $teams;
	}

	/**
	 * Swapps 2 Teams
	 *
	 * @param DrawLine $line_a
	 * @param integer  $pos_a
	 * @param DrawLine $line_b
	 * @param integer  $pos_b
	 */
	public
	function swap_teams($line_a, $pos_a, $line_b, $pos_b) {

		$team_a = $line_a->getTeamOn($pos_a);
		$team_b = $line_b->getTeamOn($pos_b);

		$line_a->setTeamOn($pos_a, $team_b);
		$line_b->setTeamOn($pos_b, $team_a);
	}

	/**
	 * Swapps 2 Adjudicator
	 *
	 * @param DrawLine $line_a
	 * @param integer  $pos_a
	 * @param DrawLine $line_b
	 * @param integer  $pos_b
	 */
	public
	function swap_adjudicator($line_a, $pos_a, $line_b, $pos_b) {

		$adju_a = $line_a->getAdjudicator($pos_a);
		$adju_b = $line_b->getAdjudicator($pos_b);

		$line_a->setAdjudicator($pos_a, $adju_b);
		$line_b->setAdjudicator($pos_b, $adju_a);
	}

	/**
	 * Finds the best team swap for a team
	 *
	 * @param DrawLine $line_a
	 * @param integer  $pos_a
	 *
	 * @return boolean
	 */
	public
	function find_best_team_swap_for($line_a, $pos_a) {
		/** @var Team $team_a */
		$team_a = $line_a->getTeamOn($pos_a);
		$best_effect = 0;
		$best_team_b_line = false;
		$best_team_b_pos = false;

		$team_a_badness = Team::getPositionBadness($pos_a, $team_a);

		$maxIterations = count($this->DRAW);
		for ($lineIterator = 0; $lineIterator < $maxIterations; $lineIterator++) {
			for ($teamIterator = 0; $teamIterator < 4; $teamIterator++) {
				//foreach ($this->DRAW as $line) {
				//foreach ($line->getTeams() as $pos_b => $team_b) { //this loop especially can be limited
				if (Team::is_swappable_with(
					$team_a,
					$this->DRAW[$lineIterator]->teams[$teamIterator],
					$line_a->level,
					$this->DRAW[$lineIterator]->level)
				) {

					//Get Status Quo Badness
					$current = $team_a_badness + Team::getPositionBadness($teamIterator, $this->DRAW[$lineIterator]->teams[$teamIterator]);
					//How it would look like
					$future = Team::getPositionBadness($teamIterator, $team_a) + Team::getPositionBadness($pos_a, $this->DRAW[$lineIterator]->teams[$teamIterator]);

					$net_effect = $future - $current;
					if ($net_effect < $best_effect) {
						$best_effect = $net_effect;
						$best_team_b_line = $this->DRAW[$lineIterator];
						$best_team_b_pos = $teamIterator;
					}
				}
			}
		}

		if ($best_team_b_line && $best_team_b_pos) {
			$this->swap_teams($line_a, $pos_a, $best_team_b_line, $best_team_b_pos);
			return true;
		}
		return false;
	}

	/**
	 * Finds the best adjudicator swap
	 *
	 * @param DrawLine $line_a
	 * @param integer  $pos_a
	 *
	 * @return bool
	 */
	public
	function find_best_adju_swap_for($line_a, $pos_a) {

		Yii::beginProfile("find_best_adju_swap_for");
		$best_effect = 0;
		$best_adju_b_line = false;
		$best_adju_b_pos = false;

		$line_a_strength = $line_a->getStrength();

		$maxIterations = count($this->DRAW);
		for ($lineIterator = 0; $lineIterator < $maxIterations; $lineIterator++) {
			$maxAdjudicator = count($this->DRAW[$lineIterator]->getAdjudicators());
			for ($adjuIterator = 0; $adjuIterator < $maxAdjudicator; $adjuIterator++) {

				$currentEnergy = $line_a_strength + $this->DRAW[$lineIterator]->getStrength();

				//Create new lines for future
				$new_line_a = $line_a;
				$new_line_b = $this->DRAW[$lineIterator];

				//Swap the adjudicators
				$this->swap_adjudicator($new_line_a, $pos_a, $new_line_b, $adjuIterator);

				//Calculate New Energy Levels
				$this->calcEnergyLevel($new_line_a);
				$this->calcEnergyLevel($new_line_b);
				$futureEnergy = $new_line_a->getStrength() + $new_line_b->getStrength();

				$net_effect = $futureEnergy - $currentEnergy;
				if ($net_effect < $best_effect) {
					$best_effect = $net_effect;
					$best_adju_b_line = $this->DRAW[$lineIterator];
					$best_adju_b_pos = $adjuIterator;
				}
			}
		}

		Yii::endProfile("find_best_adju_swap_for");
		if ($best_adju_b_line && $best_adju_b_pos && $best_adju_b_line != $line_a && $best_adju_b_pos != $pos_a) {
			$this->swap_adjudicator($line_a, $pos_a, $best_adju_b_line, $best_adju_b_pos);
			return true;
		}
		return false;
	}

	/**
	 * Sets up the variables in the EnergyConfig
	 *
	 * @param \common\models\Tournament $tournament
	 *
	 * @return bool
	 * @throws \yii\base\Exception
	 * @internal param $Tournament
	 */
	public function setup($tournament) {
		$tid = $tournament->id;
		$config = [
			[
				"label" => Yii::t("app", "Max Iterations to improve the Adjudicator Allocation"),
				"key" => "max_iterations",
				"value" => 20000,
			],
			[
				"label" => Yii::t("app", "Team and adjudicator in same society penalty"),
				"key" => "society_strike",
				"value" => 1000,
			],
			[
				"label" => Yii::t("app", "Both Adjudicators are clashed"),
				"key" => "adjudicator_strike",
				"value" => 1000,
			],
			[
				"label" => Yii::t("app", "Team with Adjudicator is clashed"),
				"key" => "team_strike",
				"value" => 1000,
			],
			[
				"label" => Yii::t("app", "Adjudicator is not allowed to chair"),
				"key" => "non_chair",
				"value" => 1000,
			],
			[
				"label" => Yii::t("app", "Chair is not perfect at the current situation"),
				"key" => "chair_not_perfect",
				"value" => 100,
			],
			[
				"label" => Yii::t("app", "Adjudicator has seen the team already"),
				"key" => "judge_met_team",
				"value" => 50,
			],
			[
				"label" => Yii::t("app", "Adjudicator has already judged in this combination"),
				"key" => "judge_met_judge",
				"value" => 50,
			]
		];

		foreach ($config as $c) {
			$strike = new EnergyConfig();
			$strike->tournament_id = $tid;
			$strike->label = $c["label"];
			$strike->key = $c["key"];
			$strike->value = $c["value"];
			if (!$strike->save())
				throw new Exception("Error saving EnergyConfig " . print_r($strike->getErrors(), true));
		}
		return true;
	}

	/**
	 * Calculates an Energy Level based on all Methods that start with "energyRule_"
	 *
	 * @param DrawLine $line
	 *
	 * @return DrawLine
	 */
	public
	function calcEnergyLevel($line) {
		$line->energyLevel = 0;
		$line->messages = [];
		foreach (get_class_methods($this) as $function) {
			if (strpos($function, "energyRule_") === 0) {
				$line = \call_user_func([StrictWUDCRules::className(), $function], $line);
			}
		}
		return $line;
	}

	/**
	 * Adds the society strike penalty
	 *
	 * @param DrawLine $line
	 *
	 * @return DrawLine
	 */
	public
	function energyRule_SameSocietyStrikes($line) {

		$penalty = $this->energyConfig["society_strike"]; // EnergyConfig::get("society_strike", $this->tournament_id);
		foreach ($line->getAdjudicators() as $adjudicator) {
			foreach ($line->getTeams() as $team) {
				if ($team["society_id"] == $adjudicator["society_id"]) {
					$line->addMessage("error", Yii::t("app", "Adjudicator {adju} and {team} in same society (+{pen})", [
						"adju" => $adjudicator["name"],
						"team" => $team["name"],
						"pen" => $penalty,
					]));
					$line->energyLevel += $penalty;
				}
			}
		}

		return $line;
	}

	/**
	 * Adds the adjudicator strike penalty
	 *
	 * @param DrawLine $line
	 *
	 * @return DrawLine
	 */
	public
	function energyRule_AdjudicatorStrikes($line) {

		$penalty = $this->energyConfig["adjudicator_strike"]; //EnergyConfig::get("adjudicator_strike", $this->tournament_id);

		foreach ($line->getAdjudicators() as $adjudicator) {
			foreach ($adjudicator["strikedAdjudicators"] as $adjudicator_check) {
				if ($adjudicator["id"] == $adjudicator_check["id"]) {
					$line->addMessage("error", Yii::t("app", "Adjudicator {adju1} and {adju2} are manually clashed (+{penalty})", [
						"adju1" => $adjudicator["name"],
						"adju2" => $adjudicator_check["name"],
						"penalty" => $penalty,

					]));
					$line->energyLevel += $penalty;
				}
			}
		}

		return $line;
	}

	/**
	 * Adds the adjudicator <-> team strike penalty
	 *
	 * @param DrawLine $line
	 *
	 * @return DrawLine
	 */
	public
	function energyRule_TeamAdjStrikes($line) {

		$penalty = $this->energyConfig["team_strike"]; // EnergyConfig::get("team_strike", $this->tournament_id);

		foreach ($line->getAdjudicators() as $adjudicator) {
			foreach ($adjudicator["strikedTeams"] as $team_check) {
				foreach ($line->getTeams() as $team) {
					if ($team["id"] == $team_check["id"]) {
						$line->addMessage("error", Yii::t("app", "Adjudicator {adju} and Team {team} are manually clashed (+{penalty})", [
							"adju" => $adjudicator["name"],
							"team" => $team["name"],
							"penalty" => $penalty,

						]));
						$line->energyLevel += $penalty;
					}
				}
			}
		}

		return $line;
	}

	/**
	 * Adds the non-chair in the chair penalty
	 *
	 * @param DrawLine $line
	 *
	 * @return DrawLine
	 */
	public
	function energyRule_NonChair($line) {

		$penalty = $this->energyConfig["non_chair"]; // EnergyConfig::get("non_chair", $this->tournament_id);
		//This relies on there being a 'can_chair' tag
		if ($line->getChair()["can_chair"] == 0) {
			$line->addMessage("error", Yii::t("app", "Adjudicator {adju} has been labelled a non-chair (+{penalty})", [
				"adju" => $line->getChair()["name"],
				"penalty" => $penalty,
			]));
			$line->energyLevel += $penalty;
		}

		return $line;
	}

	/**
	 * Adds the chair not perfect penalty
	 *
	 * @param DrawLine $line
	 *
	 * @return DrawLine
	 */
	public
	function energyRule_NotPerfect($line) {

		$penalty = $this->energyConfig["chair_not_perfect"]; //EnergyConfig::get("chair_not_perfect", $this->tournament_id);

		//This basically adds a penalty for each point away from the maximum the chair's ranking is
		$diffPerfect = (Adjudicator::MAX_RATING - $line->getChair()["strength"]);

		if ($diffPerfect > 0) {
			$comp_penalty = ($penalty * $diffPerfect);
			$line->addMessage("warning", Yii::t("app", "Chair not perfect by {points} (+{penalty})", [
				"points" => $diffPerfect,
				"penalty" => $comp_penalty,
			]));
			$line->energyLevel += $comp_penalty;
		}
		return $line;
	}

	/**
	 * Adds the judge met judge penalty
	 *
	 * @param DrawLine $line
	 *
	 * @return DrawLine
	 */
	public
	function energyRule_JudgeMetJudge($line) {

		$penalty = $this->energyConfig["judge_met_judge"]; // EnergyConfig::get("judge_met_judge", $this->tournament_id);
		$found = [];
		foreach ($line->getAdjudicators() as $adjudicator) {
			foreach ($line->getAdjudicators() as $adjudicator_match) {

				if ($adjudicator_match["id"] != $adjudicator["id"]) {
					if (in_array($adjudicator_match["id"], $adjudicator["pastAdjudicatorIDs"])) {

						if (!in_array($adjudicator_match["id"], $found)) {
							$found[] = $adjudicator_match["id"];
							$line->addMessage("warning", Yii::t("app", "Adjudicator {adju1} and {adju2} have judged together before (+{penalty})", [
								"adju1" => $adjudicator["name"],
								"adju2" => $adjudicator_match["name"],
								"penalty" => $penalty,
							]));
							$line->energyLevel += $penalty;
						}
					}
				}

			}

		}
		return $line;
	}

	/**
	 * Adds the judge met team penalty
	 *
	 * @param DrawLine $line
	 *
	 * @return DrawLine
	 */
	public
	function energyRule_JudgeMetTeam($line) {

		$penalty = $this->energyConfig["judge_met_team"]; // EnergyConfig::get("judge_met_team", $this->tournament_id);
		foreach ($line->getAdjudicators() as $adjudicator) {
			foreach ($line->getTeams() as $team) {
				if (in_array($team["id"], $adjudicator["pastTeamIDs"])) {
					$line->addMessage("warning", Yii::t("app", "Adjudicator {adju} has judged Team {team} before (+{penalty})", [
						"adju" => $adjudicator["name"],
						"team" => $team["name"],
						"penalty" => $penalty,
					]));
					$line->energyLevel += $penalty;
				}
			}
		}

		return $line;
	}

	/**
	 * Adds the panel strength penalty
	 *
	 * @param DrawLine $line
	 *
	 * @return DrawLine
	 */
	public
	function energyRule_PanelSteepness($line) {

		//$penalty = EnergyConfig::get("panel_steepness", $round->tournament_id);

		//First, we need to calculate how good the room is

		$roomPotential = $line->getLevel() - ($this->round_number - 1) * 2;

		// This will convert the level of the room into +1, +2, -1 etc. This is useful, because we want the judging to be relative to this level.
		// The equation we use is: SD = (x-1)*log(abs((x-1)))+1, where x is the 'Room Potential' and SD is the number of SDs that average is from the mean.

		$roomDifference = max(($roomPotential - 1) * log(abs(($roomPotential - 1))) + 1, 100);

		//So now we need to work out where this sits on the scale

		$comparison_factor = ($line->getStrength() - $this->average_adjudicator_strength) / $this->SD_of_adjudicators;

		$penalty = intval(pow(($roomDifference - $comparison_factor), 2));
		$line->addMessage("notice", Yii::t("app", "Steepness Comparison: {comparison_factor}, Difference: {roomDifference} (+{penalty})", [
			"comparison_factor" => round($comparison_factor, 3),
			"roomDifference" => round($roomDifference, 3),
			"penalty" => $penalty,
		]));
		$line->energyLevel += $penalty;
		return $line;
	}


}
