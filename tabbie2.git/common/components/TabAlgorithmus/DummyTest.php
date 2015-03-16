<?php

namespace common\components\TabAlgorithmus;

use \common\components\TabAlgorithmus;
use common\models\Debate;
use yii\base\Exception;
use common\models\DrawLine;

class DummyTest extends TabAlgorithmus {

	public function makeDraw($venues, $teams, $adjudicators, $preset_panels = null) {

		/**
		 * The Draw
		 */
		$DRAW = array();

		$active_rooms = (count($teams) / 4);
		if (count($teams) % 4 != 0)
			throw new Exception("Amount of active Teams must be divided by 4 ;) - (active: " . count($teams) . ")", "500");
		if ($active_rooms > count($venues))
			throw new Exception("Not enough active Rooms (active:" . count($venues) . " required:" . $active_rooms . ")", "500");
		if ($active_rooms > count($adjudicators))
			throw new Exception("Not enough adjudicators (active:" . count($adjudicators) . " min-required:" . $active_rooms . ")", "500");


		$iterateAdj = 0; //Because of ++
		$iterateVenue = 0;
		for ($iterateTeam = 0; $iterateTeam < count($teams); $iterateTeam = $iterateTeam + 4) {

			$line = new DrawLine();
			$line->venue = $venues[$iterateVenue];
			$line->setTeams($teams[$iterateTeam], $teams[$iterateTeam + 1], $teams[$iterateTeam + 2], $teams[$iterateTeam + 3]);

			if (isset($adjudicators[$iterateAdj])) {
				$line->setChair($adjudicators[$iterateAdj]);
			}

			$DRAW[] = $line;
			$iterateAdj++;
			$iterateVenue++;
		}

		/** Fill up Adjudicators */
		$lineCount = 0;
		while ($iterateAdj < count($adjudicators)) {

			if (isset($adjudicators[$iterateAdj])) {
				$DRAW[$lineCount]->addAdjudicator($adjudicators[$iterateAdj]);
			}
			$iterateAdj++;
			$lineCount++;
			if ($lineCount > count($DRAW))
				$lineCount = 0; //start at beginning
		}

		return $DRAW;
	}

	/**
	 * @param Debate $debate
	 * @param type   $name Description
	 *
	 * @return boolean
	 */
	public function calcEnergyLevel($debate) {
		$tournament = $debate->tournament;
		$debate->energy = rand(1, 100);
		return true;
	}

	/**
	 * Sets up the variables in the EnergyConfig
	 *
	 * @param \common\models\Tournament $tournament
	 */
	public function setup($tournament) {
		$tid = $tournament->id;

		$strike = new \common\models\EnergyConfig();
		$strike->tournament_id = $tid;
		$strike->label = "Strike Penalty";
		$strike->key = "strike";
		$strike->value = -1000;
		$strike->save();

		return true;
	}

}
