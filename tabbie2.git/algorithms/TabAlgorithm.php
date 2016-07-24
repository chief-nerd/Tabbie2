<?php

namespace algorithms;

use common\models\DrawLine;

/**
 * Abstract Class TabAlgorithm
 *
 * @package algorithm
 */
abstract class TabAlgorithm extends \yii\base\Object
{

    /**
     * The temporary Draw element to work on
     *
     * @var DrawLine[]
     */
    protected $DRAW = [];

    /**
     * Tournament ID from the Database
     *
     * @var integer
     */
    public $tournament_id;

    /**
     * The loaded Energy Config from the DB
     *
     * @var array
     */
    public $energyConfig;

    /**
     * @var float
     */
    public $temp = 1.0;

    /**
     * @var int
     */
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
     * Standard deviation of the Adjudicator Strength
     *
     * @var float
     */
    public $SD_of_adjudicators;

    /**
     * Array of Distribution of Adjudicator Strength
     *
     * @var float
     */
    public $strengthArray;

    /**
     * Array of Distribution of Debates Strength
     *
     * @var float
     */
    public $pointsArray;

    /**
     * Setter Class for the Draw
     *
     * @see Round::improveAdjudicator
     *
     * @param $draw DrawLine[]
     */
    public function setDraw($draw)
    {
        $this->DRAW = $draw;
    }

    abstract public function makeDraw($venues, $teams, $adjudicators, $preset_panels = []);

    abstract public function optimiseAdjudicatorAllocation($max_runs = null, $temp = null);

    abstract public function setup($tournament);

    abstract public function calcEnergyLevel($line);
}