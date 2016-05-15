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

/**
 * Class StrictWUDCRules Global Optima
 * Comments for abstract classes are marked with inheritdoc
 *
 * @package algorithm\algorithms
 */
class StrictWUDCRulesGlobalOptima extends StrictWUDCRules
{

	public static function title()
	{
		return "Strict WUDC Rules Global Optima";
	}

	public static function version()
	{
		return null; //Will make it disappear
	}

	/**
	 * @override
	 *
	 * @param null $max_runs
	 * @param null $temp
	 *
	 * @return \algorithms\DrawLine[]
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
			throw new Exception("No rows in draw");

		if ($max_runs == null || !is_int($max_runs))
			$max_runs = $this->energyConfig["max_iterations"];

		$best_draw = $DRAW;
		$this->best_energy = DrawLine::getDrawEnergy($DRAW);
		$best_moment = 0;

		return $best_draw;
	}
}
