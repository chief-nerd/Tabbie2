<?php

namespace algorithms\algorithms;

use algorithms\TabAlgorithm;
use common\models\Adjudicator;
use common\models\DrawLine;
use common\models\EnergyConfig;
use common\models\Team;
use common\models\Venue;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * Class StrictWUDCRules
 * Comments for abstract classes are marked with inheritdoc.
 *
 * @package algorithm\algorithms
 */
class RawPowerPair extends TabAlgorithm
{
	/**
	 * @inheritdoc
	 */
	public static function title()
	{
		return "Raw Power Pairing without Adjudicator Optimisation";
	}

	/**
	 * @inheritdoc
	 */
	public static function version()
	{
		return "1.0";
	}

	/* * * * * * * * * * * * *
	 *
	 * Draw Section
	 *
	 * * * * * * * * * * * * */

	/**
	 * @inheritdoc
	 */
	public function makeDraw($venues, $teams, $adjudicators, $preset_panels = [])
	{

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
		 * Sort PresetPanel
		 */
		$preset_panels = $this->sort_preset_panel($preset_panels);

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
		$presetCount = 0;
		$adju_setted = 0;

		Yii::trace(count($adjudicators) . " Adjudicators to set in DRAW", __METHOD__);
		Yii::trace(count($preset_panels) . " PP to set in DRAW", __METHOD__);

		while ($toAdd = array_shift($adjudicators)) {

			//Yii::trace("Enter line: ".$lineID." with presetIndex: ".$presetCount, __METHOD__);
			$beforePP = $this->DRAW[$lineID]->hasPresetPanel;

			Yii::trace("Add " . $toAdd["name"] . " to " . $lineID, __METHOD__);
			$this->DRAW[$lineID]->addAdjudicator($toAdd);
			$adju_setted++;

			if (isset($preset_panels[$presetCount]) && $this->DRAW[$lineID]->hasPresetPanel != true) {
				$currentStrength = $this->DRAW[$lineID]->getStrength();

				if ($currentStrength < $preset_panels[$presetCount]["strength"] &&
					count($preset_panels[$presetCount]["adju"]) <= count($this->DRAW[$lineID]->getAdjudicators())
				) {
					$freeAdju = $this->DRAW[$lineID]->getAdjudicators();
					Yii::trace("Old Line adjus: " . implode(", ", ArrayHelper::getColumn($this->DRAW[$lineID]->getAdjudicators(), "name")), __METHOD__);
					Yii::trace("Setting PP on line " . $lineID . " with " .
						implode(", ", ArrayHelper::getColumn($preset_panels[$presetCount]["adju"], "name")) .
						" with id " . $preset_panels[$presetCount]["id"],
						__METHOD__
					);
					$this->DRAW[$lineID]->overrideAdjudicators($preset_panels[$presetCount]["adju"]);
					$this->DRAW[$lineID]->hasPresetPanel = true; //Prevent moving!
					$this->DRAW[$lineID]->offLimit = count($preset_panels[$presetCount]["adju"]) - 1;
					$this->DRAW[$lineID]->panelID = $preset_panels[$presetCount]["id"];
					$presetCount++;

					//Chair goes on stack last -> so first out
					$freeAdju = array_reverse($freeAdju);
					//Put free Adjus back on the stack
					foreach ($freeAdju as $fa) {
						array_unshift($adjudicators, $fa);
						$adju_setted--;
					}
				}
			}
			$afterPP = $this->DRAW[$lineID]->hasPresetPanel;
			if ($afterPP != $beforePP)
				Yii::trace(">> Line " . $lineID . " dont match PP flag - might be ok", __METHOD__);

			$lineID = $this->nextLine($lineID);
		}
		Yii::trace($adju_setted . " Adjudicator placed in DRAW", __METHOD__);
		Yii::trace($presetCount . " PP placed in DRAW", __METHOD__);

		if ($presetCount < count($preset_panels)) {
			Yii::trace("There are still " . (count($preset_panels) - $presetCount) . " PP left", __METHOD__);
			//still some left - maybe not a full drawline run? fill up!
			for ($i = $presetCount; $i < count($preset_panels); $i++) {
				$freeAdju = $this->DRAW[$lineID]->getAdjudicators();
				$this->DRAW[$lineID]->overrideAdjudicators($preset_panels[$i]["adju"]);
				$this->DRAW[$lineID]->hasPresetPanel = true; //Prevent moving!
				$this->DRAW[$lineID]->offLimit = count($preset_panels[$i]["adju"]) - 1;
				$this->DRAW[$lineID]->panelID = $preset_panels[$presetCount]["id"];
				if (count($freeAdju) > 0) {
					$freeAdju = array_reverse($freeAdju);
					foreach ($freeAdju as $adj) {
						$lineID = $this->nextLine($lineID); //First new line
						$this->DRAW[$lineID]->addAdjudicator($adj);
					}
				}
			}
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

		$this->optimiseAdjudicatorAllocation($this->DRAW);

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
	 * Sort Adjudicator
	 *
	 * @param Adjudicator[] $adj
	 *
	 * @return Adjudicator[]
	 */
	public function sort_adjudicators($adj)
	{
		usort($adj, ['common\models\Adjudicator', 'compare_strength']);

		return $adj;
	}

	/**
	 * Sort Teams
	 *
	 * @param Team[] $teams
	 *
	 * @return Team[]
	 */
	public function sort_teams($teams)
	{
		//Surpress an error due to a php bug.
		usort($teams, ['common\models\Team', 'compare_points']);

		return $teams;
	}

	/**
	 * Sort preset panels
	 *
	 * @param Panel[] $panels
	 *
	 * @return Panel[]
	 */
	public function sort_preset_panel($panels)
	{
		usort($panels, ['common\models\Panel', 'compare_length_strength']);

		return $panels;
	}

	/**
	 * Randomises the Teams within Team point brackets
	 *
	 * @param Team[] $teams
	 *
	 * @return Team[]
	 */
	public function randomise_within_points($teams)
	{

		$saved_points = $teams[0]["points"]; //reset to start
		$last_break = 0;

		for ($i = 0; $i < count($teams); $i++) {
			$team_points = $teams[$i]["points"];
			if ($team_points != $saved_points || $i == (count($teams) - 1)) {
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
	 * Finds the best team swap for a team
	 *
	 * @param DrawLine $line_a
	 * @param integer  $pos_a
	 *
	 * @return boolean
	 */
	public function find_best_team_swap_for($line_a, $pos_a)
	{
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
	 * Swapps 2 Teams
	 *
	 * @param DrawLine $line_a
	 * @param integer  $pos_a
	 * @param DrawLine $line_b
	 * @param integer  $pos_b
	 */
	public function swap_teams($line_a, $pos_a, $line_b, $pos_b)
	{
		$team_a = $line_a->getTeamOn($pos_a);
		$team_b = $line_b->getTeamOn($pos_b);

		$line_a->setTeamOn($pos_a, $team_b);
		$line_b->setTeamOn($pos_b, $team_a);
	}

	/**
	 * Sorts the Draw so strongest room is on top
	 *
	 * @param DrawLine[] $draw
	 *
	 * @return DrawLine[]
	 */
	public function sort_draw_by_points($draw)
	{
		usort($draw, ['common\models\DrawLine', "compare_points"]);

		return $draw;
	}

	private function nextLine($current_line)
	{
		if (isset($this->DRAW[$current_line + 1])) //Is there a next line
			return ($current_line + 1);
		else
			return 0; //Start again at beginning
	}

	/**
	 * @inheritdoc
	 */
	public function calcEnergyLevel($line)
	{
		$line->energyLevel = 0;
		$line->messages = [];

		return $line;
	}

	/**
	 * @inheritdoc
	 */
	public function optimiseAdjudicatorAllocation($max_runs = null, $temp = null)
	{
		$DRAW = &$this->DRAW;

		if ($temp === null)
			$this->temp = 1.0;
		else
			$this->temp = $temp;

		$maxDrawIterations = count($DRAW);
		if ($maxDrawIterations == 0)
			throw new Exception("No rows in draw " . print_r($this->DRAW, true));

		Yii::trace("Draw Rows to improve: " . $maxDrawIterations, __METHOD__);

		$best_draw = $DRAW;

		return $best_draw;
	}

	/* * * * * * * * * * * * *
	 *
	 * Energy Section
	 *
	 * * * * * * * * * * * * */

	/**
	 * @inheritdoc
	 */
	public function setup($tournament)
	{
		return true;
	}
}
