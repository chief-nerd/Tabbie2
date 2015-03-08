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

    /**
     * The Energy Level of the Adjudicator Panel
     */
    public $energyLevel = 0;

    /**
     * Get the Strength of the Panel
     * @return int
     */
    public function getStrength() {
        return 1;
    }

    public function setTeams($og, $oo, $cg, $co) {
        $this->setOG($og);
        $this->setOO($oo);
        $this->setCG($cg);
        $this->setCO($co);
    }

    public function setTeamsByArray($teams) {
        if (count($teams) == 4) {
            $this->setOG($teams[0]);
            $this->setOO($teams[1]);
            $this->setCG($teams[2]);
            $this->setCO($teams[3]);
        } else
            throw new Exception("Paramter has not 4 teams");
    }

    /**
     * Return all teams on that line
     * @return Team[]
     */
    public function getTeams() {
        return [
            Team::OG => $this->teams[0],
            Team::OO => $this->teams[1],
            Team::CG => $this->teams[2],
            Team::CO => $this->teams[3],
        ];
    }

    /**
     * Return the Team on Position X
     */
    public function getTeamOn($pos) {
        return $this->teams[$pos];
    }

    /**
     * Sets a Team to a specific Position
     * @param integer $pos
     * @param Team $team
     */
    public function setTeamOn($pos, $team) {
        $this->teams[$pos] = $team;
    }

    public function setOG($team) {
        return $this->setTeamOn(Team::OG, $team);
    }

    public function getOG() {
        return $this->getTeamOn(Team::OG);
    }

    public function setOO($team) {
        return $this->setTeamOn(Team::OO, $team);
    }

    public function getOO() {
        return $this->getTeamOn(Team::OO);
    }

    public function setCG($team) {
        return $this->setTeamOn(Team::CG, $team);
    }

    public function getCG() {
        return $this->getTeamOn(Team::CG);
    }

    public function setCO($team) {
        return $this->setTeamOn(Team::CO, $team);
    }

    public function getCO() {
        return $this->getTeamOn(Team::CO);
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

    /**
     * Get the "Debate Level" aka the highest points of team in that debate
     * @return integer Points
     */
    public function getLevel() {
        return max([
            $this->getOG()->getPoints(),
            $this->getOO()->getPoints(),
            $this->getCO()->getPoints(),
            $this->getCG()->getPoints(),
        ]);
    }

}