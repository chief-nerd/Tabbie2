<?php

namespace common\models;

use Yii;
use yii\base\Exception;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 *
 * @property integer level
 */
class DrawLine extends Model
{

	const OG = 1;
	const OO = 2;
	const CG = 3;
	const CO = 4;

	/** @var  integer DebateID */
	public $id;

	/**
	 * @var Venue
	 */
	public $venue;

	/**
	 * Teams in the Debate 0=>OG, 1=>OO, 2=>CG, 3=>CO,
	 *
	 * @var array
	 */
	private $teams = [];

	/**
	 * Adjudicators in that debate, Position 0 is the chair
	 *
	 * @var array
	 */
	public $adj = [];

	/**
	 * Flag that marks if the panel already existed and should be saved
	 *
	 * @var boolean
	 */
	public $hasPresetPanel = false;

	/**
	 * Holds the Panel ID
	 *
	 * @var integer
	 */
	public $panelID;

	/**
	 * The Energy Level of the Adjudicator Panel
	 */
	public $energyLevel;

	/**
	 * Messages for that line to show to user
	 *
	 * @var array
	 */
	public $messages = [];

	/**
	 * Get the Average Strength of the Panel
	 *
	 * @return float
	 */
	public function getStrength()
	{
		$total = 0;
		$n = 0;
		foreach ($this->adj as $adj) {
			$total += $adj["strength"];
			$n++;
		}

		return intval($total / $n);
	}

	public function addMessage($key, $msg, $penalty = null)
	{
		$this->messages[] = [
			"key"     => $key,
			"msg"     => $msg,
			"penalty" => $penalty,
		];
	}

	public function setTeams($og, $oo, $cg, $co)
	{
		$this->setOG($og);
		$this->setOO($oo);
		$this->setCG($cg);
		$this->setCO($co);
	}

	public function setTeamsByArray(array $teams)
	{
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
	 *
	 * @return Team[]
	 */
	public function getTeams()
	{
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
	public function getTeamOn($pos)
	{
		return $this->teams[$pos];
	}

	/**
	 * Sets a Team to a specific Position
	 *
	 * @param integer $pos
	 * @param Team    $team
	 */
	public function setTeamOn($pos, $team)
	{
		$this->teams[$pos] = $team;
	}

	public function setOG($team)
	{
		return $this->setTeamOn(Team::OG, $team);
	}

	public function getOG()
	{
		return $this->getTeamOn(Team::OG);
	}

	public function setOO($team)
	{
		return $this->setTeamOn(Team::OO, $team);
	}

	public function getOO()
	{
		return $this->getTeamOn(Team::OO);
	}

	public function setCG($team)
	{
		return $this->setTeamOn(Team::CG, $team);
	}

	public function getCG()
	{
		return $this->getTeamOn(Team::CG);
	}

	public function setCO($team)
	{
		return $this->setTeamOn(Team::CO, $team);
	}

	public function getCO()
	{
		return $this->getTeamOn(Team::CO);
	}

	public function setChair($adj)
	{
		$this->adj[0] = $adj;
	}

	public function getChair()
	{
		return $this->adj[0];
	}

	public function addAdjudicator($adj)
	{
		$this->adj[] = $adj;
	}

	public function addChair($adj)
	{
		array_unshift($this->adj, $adj);
	}

	/**
	 * Get all Adjudicators with Chair in first array spot.
	 *
	 * @return Adjudicator[]
	 */
	public function getAdjudicators()
	{
		return $this->adj;
	}

	public function getAdjudicator($i)
	{
		if (isset($this->adj[$i]))
			return $this->adj[$i];
		else
			throw new Exception("Adjudicator not set for this line");
	}

	public function setAdjudicator($i, $adjudicator)
	{
		$this->adj[$i] = $adjudicator;
	}

	public function overrideAdjudicators($adjudicators)
	{
		$this->adj = $adjudicators;
	}

	/**
	 * Get the "Debate Level" aka the highest points of team in that debate
	 *
	 * @return integer Points
	 */
	public function getLevel()
	{
		return max([
			$this->getOG()["points"],
			$this->getOO()["points"],
			$this->getCO()["points"],
			$this->getCG()["points"],
		]);
	}

	/**
	 * Compare function for point sorting
	 *
	 * @param DrawLine $a
	 * @param DrawLine $b
	 *
	 * @return integer
	 */
	public static function compare_points($a, $b)
	{
		$as = $a->getLevel();
		$bs = $b->getLevel();

		return ($as < $bs) ? 1 : (($as > $bs) ? -1 : 0);
	}

	/**
	 * @param DrawLine[] $draw
	 *
	 * @return int
	 */
	public static function getDrawEnergy($draw)
	{
		$max_lines = count($draw);
		$energy = 0;
		for ($line = 0; $line < $max_lines; $line++) {
			$energy += $draw[$line]->energyLevel;
		}

		return $energy;
	}
}
