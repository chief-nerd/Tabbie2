<?php

namespace common\components;

abstract class TabAlgorithmus extends \yii\base\Object {

	/**
	 * The Draw
	 *
	 * @var DrawLine[]
	 */
	protected $DRAW = array();

	/**
	 * Tournament ID
	 *
	 * @var integer
	 */
	public $tournament_id;

	/**
	 * Function that calculated the Draw for a round
	 *
	 * @param \common\models\Venue[]       $venues       Array of all active venues
	 * @param \common\models\Team[]        $teams        Array of all teams in the tournament in the structur of
	 *                                                   array(
	 *                                                   name,
	 *                                                   institution,
	 *                                                   )
	 * @param \common\models\Adjudicator[] $adjudicators Array of all active adujicators
	 */
	abstract public function makeDraw($venues, $teams, $adjudicators, $preset_panels = array());

	/**
	 * @param \common\models\DrawLine $line  The current DrawLine
	 */
	abstract public function calcEnergyLevel($line);

	/**
	 * @param \common\models\Debate $debate The Debate to be calculated
	 */
	abstract public function setup($tournament);
}
