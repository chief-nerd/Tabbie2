<?php

namespace common\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class DrawLine extends Model {

    const OG = 1;
    const OO = 2;
    const CG = 3;
    const CO = 4;

    /**
     *
     * @var Venue
     */
    public $venue;

    /**
     * Teams in the Debate 0=>OG, 1=>OO, 2=>CG, 3=>CO,
     * @var Team[]
     */
    private $teams;

    /**
     * Adjudicators in that debate, Position 0 is the chair
     * @var Adjudicator[]
     */
    private $adj;

    /**
     * Flag that marks if the panel already existed and should be saved
     * @var boolean
     */
    public $hasPresetPanel = false;

    /**
     * If $hasPresetPanel == true then this holds the Panel ID
     * @var integer
     */
    public $panelID;

    private function team_set($team, $pos) {
        $this->teams[$pos] = $team;
    }

    private function team_get($pos) {
        return $this->teams[$pos];
    }

    /**
     * Get the Strength of the Panel
     * @return int
     */
    public function getStrength() {
        return 0;
    }

    public function setTeams($og, $oo, $cg, $co) {
        $this->setOG($og);
        $this->setOO($oo);
        $this->setCG($cg);
        $this->setCO($co);
    }

    public function setOG($team) {
        return $this->team_set($team, DrawLine::OG);
    }

    public function getOG() {
        return $this->team_get(DrawLine::OG);
    }

    public function setOO($team) {
        return $this->team_set($team, DrawLine::OO);
    }

    public function getOO() {
        return $this->team_get(DrawLine::OO);
    }

    public function setCG($team) {
        return $this->team_set($team, DrawLine::CG);
    }

    public function getCG() {
        return $this->team_get(DrawLine::CG);
    }

    public function setCO($team) {
        return $this->team_set($team, DrawLine::CO);
    }

    public function getCO() {
        return $this->team_get(DrawLine::CO);
    }

    public function setChair($adj) {
        $this->adj[0] = $adj;
    }

    public function getChair() {
        return $this->adj[0];
    }

    public function addAdjudicator($adj) {
        $this->adj[] = $adj;
    }

    /**
     * Get all Adjudicators with Chair in first array spot.
     * @return Adjudicator[]
     */
    public function getAdjudicators() {
        return $this->adj;
    }

}
