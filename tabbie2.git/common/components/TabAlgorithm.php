<?php

namespace common\components;

abstract class TabAlgorithm extends \yii\base\Object {

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

	public $energyConfig;

	public $temp = 1.0;

	public $best_energy = 0;

	/**
	 * Current Round number
	 *
	 * @var integer
	 */
	public $round_number;

	/**
	 * Average Adjudicator Strength in the tournament
	 *
	 * @var float
	 */
	public $average_adjudicator_strength;

	/**
	 * Standard diviation of the Adjudicator Strength
	 * @var float
	 */
	public $SD_of_adjudicators;

	/**
	 * Setter Class for the Draw
	 * @see Round::improveAdjudicator
	 * @param $draw
	 */
	public function setDraw($draw) {
		$this->DRAW = $draw;
	}

	/**
	 * Function that calculated the Draw for a round

	 *
*@param \common\models\Venue[]       $venues       Array of all active venues
	 * @param \common\models\Team[]        $teams        Array of all teams in the tournament in the structur of
	 *                                                   array(
	 *                                                   name,
	 *                                                   institution,
	 *                                                   )
	 * @param \common\models\Adjudicator[] $adjudicators Array of all active adujicators
	 * @param array                        $preset_panels
	 *
	 * @return
	 */
	abstract public function makeDraw($venues, $teams, $adjudicators, $preset_panels = array());

	/**
	 * @param \common\models\DrawLine $line  The current DrawLine
	 */
	abstract public function calcEnergyLevel($line);

	/**
	 * @param \common\models\Tournament $tournament
	 */
	abstract public function setup($tournament);
}
