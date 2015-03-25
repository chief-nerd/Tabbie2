<?php

namespace common\components\TabAlgorithmus;

use common\components\TabAlgorithmus;
use common\models\Adjudicator;
use common\models\DrawLine;
use common\models\EnergyConfig;
use common\models\Round;
use common\models\Team;
use common\models\Venue;
use Yii;
use yii\base\Exception;

class StrictWUDCRules extends TabAlgorithmus {

	/**
	 * Function to calculate a draw based on WUDC strict Rules
	 *
	 * @param Venue[] $venues
	 * @param Team[]  $teams
	 * @param type    $adjudicators
	 * @param type    $preset_panels
	 *
	 * @return type
	 * @throws Exception
	 */
	public function makeDraw($venues, $teams, $adjudicators, $preset_panels = array()) {

		$active_rooms = (count($teams) / 4);
		if (count($teams) % 4 != 0)
			throw new Exception("Amount of active Teams must be divided by 4 ;) - (active: " . count($teams) . ")", "500");
		if ($active_rooms > count($venues))
			throw new Exception("Not enough active Rooms (active:" . count($venues) . " required:" . $active_rooms . ")", "500");
		if ($active_rooms > count($adjudicators))
			throw new Exception("Not enough adjudicators (active:" . count($adjudicators) . " min-required:" . $active_rooms . ")", "500");

		/**
		 * Shuffle venues
		 */
		shuffle($venues);

		/**
		 * Sort Adjudicator at Strength
		 */
		$adjudicators = $this->sort_adjudicators($adjudicators);

		/*
		  First we need to make the brackets for each debate. This means ordering the teams by the number of points.
		 */
		$teams = $this->sort_teams($teams);

		/*
		  Then, within point brackets, we randomise the teams
		 */
		$teams = $this->randomise_within_points($teams);

		/**
		 * Set Past Position Matrix
		 */
		for ($i = 0; $i < count($teams); $i++) {
			$teams[$i]->positionMatrix = $teams[$i]->getPastPositionMatrix();
		}

		/**
		 * Generate a first rough draw by running the teams down from top to bottom and allocate them
		 */
		for ($i = 0; $i < $active_rooms; $i++) {
			$line = new DrawLine();

			$choosen = array_splice($teams, 0, 4);
			shuffle($choosen);
			$line->setTeamsByArray($choosen);

			$line->venue = $venues[$i];
			$this->DRAW[] = $line;
		}


		/**
		 * Now start improving that initial set
		 * Go through the Draw until you can't make any improvements
		 */
		$stillFoundASwap = true;
		$count = 0;
		while ($stillFoundASwap) {
			$count++;
			$stillFoundASwap = false; //Assume we are done, prove me wrong

			$maxIterations = count($this->DRAW);
			for ($lineIterator = 0; $lineIterator < $maxIterations; $lineIterator++) {
				for ($teamIterator = 0; $teamIterator < 4; $teamIterator++) {
					if ($this->DRAW[$lineIterator]->teams[$teamIterator]->getPositionBadness($teamIterator) > 0) { // Not optimal positioning exists here
						if ($this->find_best_swap_for($this->DRAW[$lineIterator], $teamIterator)) { //Do we find a swap that makes it better
							$stillFoundASwap = true; //We found a better swap, do the loop again
							break;
						}
					}
				}
				if ($stillFoundASwap)
					break; //Found it already break on!
			}
			//If we havn't found a better swap $stillFoundASwap should be false and the loop breaks
		}
		Yii::trace("Finished Loop with count: $count", __METHOD__);

		/*
		 * Allocate the Adjudicators
		 */
		$lineID = 0;
		foreach ($adjudicators as $adj) {
			$this->DRAW[$lineID]->addAdjudicator($adj);

			if (isset($this->DRAW[$lineID + 1])) //Is there a next line
				$lineID++;
			else
				$lineID = 0; //Start at beginning
		}

		/*
		 * We have found the best possible combination
		 * There is no better swap possible now.
		 * Return der DRAW[] and get ready to debate
		 */
		return $this->DRAW;
	}

	/**
	 * Sortiert Teams
	 *
	 * @param Team[] $teams
	 *
	 * @return Team[]
	 */
	public function sort_teams($teams) {
		//Surpress an error due to a php bug.
		@usort($teams, array('common\models\Team', 'compare_points'));
		return $teams;
	}

	/**
	 * Sortiert Adjudicator
	 *
	 * @param Adjudicator[] $adj
	 *
	 * @return Adjudicator[]
	 */
	public function sort_adjudicators($adj) {
		usort($adj, array('common\models\Adjudicator', 'compare_strength'));
		return $adj;
	}

	/**
	 * Randomises the Teams within Teampoints
	 *
	 * @param Team[] $teams
	 *
	 * @return Team[]
	 */
	public function randomise_within_points($teams) {

		$saved_points = $teams[0]->points; //reset to start
		$last_break = 0;

		for ($i = 0; $i < count($teams); $i++) {
			$team_points = $teams[$i]->points;
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
	public function swap_teams($line_a, $pos_a, $line_b, $pos_b) {

		$team_a = $line_a->getTeamOn($pos_a);
		$team_b = $line_b->getTeamOn($pos_b);

		$line_a->setTeamOn($pos_a, $team_b);
		$line_b->setTeamOn($pos_b, $team_a);
	}

	/**
	 * Wrapper to produce easier debug infos in codeception
	 *
	 * @param type $teams
	 */
	private function debug($teams) {
		$output = "";
		foreach ($teams as $t) {
			$output .= $t->id . "/" . $t->getPoints() . "\t";
		}

		Debug::debug($output);
	}

	/**
	 * @param DrawLine $line_a
	 * @param integer  $pos_a
	 *
	 * @return boolean
	 */
	public function find_best_swap_for($line_a, $pos_a) {
		/** @var Team $team_a */
		$team_a = $line_a->getTeamOn($pos_a);
		$best_effect = 0;
		$best_team_b_line = false;
		$best_team_b_pos = false;

		$maxIterations = count($this->DRAW);
		for ($lineIterator = 0; $lineIterator < $maxIterations; $lineIterator++) {
			for ($teamIterator = 0; $teamIterator < 4; $teamIterator++) {
				//foreach ($this->DRAW as $line) {
				//foreach ($line->getTeams() as $pos_b => $team_b) { //this loop especially can be limited
				if ($team_a->is_swappable_with(
					$this->DRAW[$lineIterator]->teams[$teamIterator],
					$line_a->level,
					$this->DRAW[$lineIterator]->level)
				) {

					//Get Status Quo Badness
					$current = $team_a->getPositionBadness($pos_a) + $this->DRAW[$lineIterator]->teams[$teamIterator]->getPositionBadness($teamIterator);
					//How it would look like
					$future = $team_a->getPositionBadness($teamIterator) + $this->DRAW[$lineIterator]->teams[$teamIterator]->getPositionBadness($pos_a);

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
	 * Sets up the variables in the EnergyConfig
	 *
	 * @param \common\models\Tournament $tournament
	 *
	 * @return boolean
	 */
	public function setup($tournament) {
		$tid = $tournament->id;

		$config = [
			[
				"label" => "Team and adjudicator in same society penalty",
				"key" => "society_strike",
				"value" => -1000,
			],
			[
				"label" => "Adjudicator is Clashed",
				"key" => "adjudicator_strike",
				"value" => -1000,
			],
			[
				"label" => "Adjudicator is not allowed to chair",
				"key" => "non_chair",
				"value" => -1000,
			],
			[
				"label" => "Chair is not perfect at the current situation",
				"key" => "chair_not_perfect",
				"value" => -100,
			],
			[
				"label" => "Adjudicator has already judged in this combination",
				"key" => "judge_met_judge",
				"value" => -100,
			],
			[
				"label" => "Adjudicator has seen the team already",
				"key" => "judge_met_team",
				"value" => -20,
			]
		];

		foreach ($config as $c) {
			$strike = new EnergyConfig();
			$strike->tournament_id = $tid;
			$strike->label = $c["label"];
			$strike->key = $c["key"];
			$strike->value = $c["value"];
			$strike->save();
		}
		return true;
	}

	/**
	 * @param DrawLine $line
	 * @param Round    $round
	 *
	 * @return DrawLine
	 */
	public function calcEnergyLevel($line, $round) {
		$line->energyLevel = 0;
		foreach (get_class_methods($this) as $function) {
			if (strpos($function, "energyRule_") === 0) {
				$line = \call_user_func([StrictWUDCRules::className(), $function], $line, $round);
			}
		}
		return $line;
	}

	/**
	 * Adds the society strike penalty
	 *
	 * @param DrawLine $line
	 * @param Round    $round
	 *
	 * @return DrawLine
	 */
	public function energyRule_SameSocietyStrikes($line, $round) {

		$penalty = EnergyConfig::get("society_strike", $round->tournament_id);
		foreach ($line->getAdjudicators() as $adjudicator) {
			foreach ($line->getTeams() as $team) {
				if ($team->society_id == $adjudicator->society_id) {
					$line->addMessage("error", "Adjudicator " . $adjudicator->name . " and " . $team->name . " in same society");
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
	 * @param Round    $round
	 *
	 * @return DrawLine
	 */
	public function energyRule_AdjudicatorStrikes($line, $round) {

		$penalty = EnergyConfig::get("adjudicator_strike", $round->tournament_id);

		foreach ($line->getAdjudicators() as $adjudicator) {
			foreach ($adjudicator->getStrikedAdjudicators()->all() as $adjudicator_check) {
				if ($adjudicator_check->id == $adjudicator->id) {
					$line->addMessage("error", "Adjudicator " . $adjudicator->name . " and " . $adjudicator_check->name . " are clashed");
					$line->energyLevel += $penalty;
				}
			}
		}

		return $line;
	}

	/**
	 * Adds the non-chair in the chair penalty
	 *
	 * @param DrawLine $line
	 * @param Round    $round
	 *
	 * @return DrawLine
	 */
	public function energyRule_NonChair($line, $round) {

		$penalty = EnergyConfig::get("non_chair", $round->tournament_id);
		//This relies on there being a 'can_chair' tag
		if ($line->getChair()->can_chair == 0) {
			$line->addMessage("error", "Adjudicator " . $line->getChair()->name . " has been labelled a non-chair");
			$line->energyLevel += $penalty;
		}

		return $line;
	}

	/**
	 * Adds the chair not perfect penalty
	 *
	 * @param DrawLine $line
	 * @param Round    $round
	 *
	 * @return DrawLine
	 */
	public function energyRule_NotPerfect($line, $round) {

		$penalty = EnergyConfig::get("chair_not_perfect", $round->tournament_id);

		//This basically adds a penalty for each point away from the maximum the chair's ranking is
		$diffPerfect = (Adjudicator::MAX_RATING - $line->getChair()->strength);

		$line->addMessage("warning", "Chair not perfect by " . $diffPerfect);
		$line->energyLevel += ($penalty * $diffPerfect);

		return $line;
	}

	/**
	 * Adds the judge met judge penalty
	 *
	 * @param DrawLine $line
	 * @param Round    $round
	 *
	 * @return DrawLine
	 */
	public function energyRule_JudgeMetJudge($line, $round) {

		$penalty = EnergyConfig::get("judge_met_judge", $round->tournament_id);
		foreach ($line->getAdjudicators() as $adjudicator) {
			$pastAdjudicatorIDS = $adjudicator->getPastAdjudicatorIDs();

			foreach ($line->getAdjudicators() as $adjudicator_match) {
				if ($adjudicator_match->id != $adjudicator->id) {
					if (in_array($adjudicator_match->id, $pastAdjudicatorIDS)) {
						$line->addMessage("warning", "Adjudicator " . $adjudicator->name . " and " . $adjudicator_match->name . " have judged together before");
						$line->energyLevel += $penalty;
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
	 * @param Round    $round
	 *
	 * @return DrawLine
	 */
	public function energyRule_JudgeMetTeam($line, $round) {

		$penalty = EnergyConfig::get("judge_met_team", $round->tournament_id);
		foreach ($line->getAdjudicators() as $adjudicator) {
			$pastTeamIDs = $adjudicator->getPastTeamIDs();

			foreach ($line->getTeams() as $team) {
				if (in_array($team->id, $pastTeamIDs)) {
					$line->addMessage("warning", "Adjudicator " . $adjudicator->name . " has judged Team " . $team->name . " before");
					$line->energyLevel += $penalty;
				}
			}
		}

		return $line;
	}
}
