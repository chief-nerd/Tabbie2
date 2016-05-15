<?php

namespace algorithms\algorithms;

use algorithms\TabAlgorithm;

/**
 * Class DummyTest
 * Comments for abstract classes are marked with inheritdoc
 *
 * @package algorithm\algorithms
 */
class DummyTest extends TabAlgorithm
{

	/**
	 * @inheritdoc
	 */
	public static function title()
	{
		return "Dummy Class";
	}

	/**
	 * @inheritdoc
	 */
	public static function version()
	{
		//Don't show = use null
		return null;
	}

	/**
	 * @inheritdoc
	 */
	public function makeDraw($venues, $teams, $adjudicators, $preset_panels = null)
	{

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

		/* Fill up Adjudicators */
		$lineCount = 0;
		while ($iterateAdj < count($adjudicators)) {

			if (isset($adjudicators[$iterateAdj])) {
				$this->DRAW[$lineCount]->addAdjudicator($adjudicators[$iterateAdj]);
			}
			$iterateAdj++;
			$lineCount++;
			if ($lineCount > count($this->DRAW))
				$lineCount = 0; //start at beginning
		}

		return $this->DRAW;
	}

	/**
	 * @inheritdoc
	 */
	public function optimiseAdjudicatorAllocation($max_runs = null, $temp = null)
	{
		return $this->DRAW;
	}


	/**
	 * @inheritdoc
	 */
	public function setup($tournament)
	{
		/*
		$tid = $tournament->id;
		$strike = new \common\models\EnergyConfig();
		$strike->tournament_id = $tid;
		$strike->label = "Strike Penalty";
		$strike->key = "strike_key";
		$strike->value = 1000;
		$strike->save();
		*/

		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function calcEnergyLevel($line)
	{
		$line->energyLevel = rand(1, 100);
		$line->messages = [];

		return $line;
	}
}