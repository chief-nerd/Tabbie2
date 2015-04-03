<?php

namespace common\models;

use Yii;
use yii\base\Exception;

/**
 * This is the model class for table "team".

 *
*@property integer       $id
 * @property string        $name
 * @property integer       $active
 * @property integer       $tournament_id
 * @property integer       $speakerA_id
 * @property integer       $speakerB_id
 * @property integer       $society_id
 * @property integer       $isSwing
 * @property integer       $language_status
 * @property integer       $points
 * @property integer       $speakerA_speaks
 * @property integer       $speakerB_speaks
 * @property integer       $speakerA_checkedin
 * @property integer       $speakerB_checkedin
 * @property TabPosition[] $tabPositions
 * @property InSociety     $inSocieties
 * @property Adjudicator[] $adjudicators
 * @property Tournament    $tournament
 * @property User          $speakerA
 * @property User          $speakerB
 */
class Team extends \yii\db\ActiveRecord {

	const OG = 0;
	const OO = 1;
	const CG = 2;
	const CO = 3;

	public $positionMatrix;

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'team';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['tournament_id', 'name', 'society_id'], 'required'],
			['speakerA_id', 'required', 'whenClient' => "function (attribute, value) {
        return $('#team-isswing')[0].checked == false;
    }"],
			['speakerB_id', 'required', 'whenClient' => "function (attribute, value) {
        return $('#team-isswing')[0].checked == false;
    }"],
			[['speakerA_id', 'speakerB_id'], 'default'],
			[['tournament_id', 'active', 'society_id', 'isSwing', 'language_status', 'points', 'speakerA_speaks', 'speakerB_speaks'], 'integer'],
			[['name'], 'string', 'max' => 255]
		];
	}

	/**
	 * Call before model save
	 *
	 * @param type $insert
	 *
	 * @return boolean
	 */
	public function beforeSave($insert) {
		if (parent::beforeSave($insert)) {
			if ($insert === true) //Do only on new Record
			{
				if ($this->speakerA->language_status == User::LANGUAGE_ENL && $this->speakerB->language_status == User::LANGUAGE_ENL)
					$this->language_status = User::LANGUAGE_ENL;
				else if ($this->speakerA->language_status == User::LANGUAGE_ESL && $this->speakerB->language_status == User::LANGUAGE_ESL)
					$this->language_status = User::LANGUAGE_ESL;
				else if ($this->speakerA->language_status == User::LANGUAGE_EFL && $this->speakerB->language_status == User::LANGUAGE_EFL)
					$this->language_status = User::LANGUAGE_EFL;
				else $this->language_status = User::LANGUAGE_NONE;
			}
			return true;
		}

		return false;
	}

	/**
	 * @inheritdoc
	 * @return VTAQuery
	 */
	public static function find() {
		return new VTAQuery(get_called_class());
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('app', 'ID'),
			'name' => Yii::t('app', 'Team Name'),
			'active' => Yii::t('app', 'Active'),
			'tournament_id' => Yii::t('app', 'Tournament ID'),
			'speakerName' => Yii::t('app', 'Speaker Name'),
			'speakerA_id' => Yii::t('app', 'Speaker A'),
			'speakerB_id' => Yii::t('app', 'Speaker B'),
			'societyName' => Yii::t('app', 'Society Name'),
			'society_id' => Yii::t('app', 'Society'),
			'isSwing' => Yii::t('app', 'Swing Team'),
			'language_status' => Yii::t('app', 'Language Status'),
		];
	}

	public function getSocietyName() {
		return $this->society->fullname;
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTabPositions() {
		return $this->hasMany(TabPosition::className(), ['team_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getStrikedAdjudicators() {
		return $this->hasMany(Adjudicator::className(), ['id' => 'adjudicator_id'])
		            ->viaTable('team_strike', ['team_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTournament() {
		return $this->hasOne(Tournament::className(), ['id' => 'tournament_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getSpeakerA() {
		return $this->hasOne(User::className(), ['id' => 'speakerA_id'])->from('user uA');
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getSpeakerB() {
		return $this->hasOne(User::className(), ['id' => 'speakerB_id'])->from('user uB');
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getInSocieties() {
		return InSociety::find()->where("user_id IN (:userA, :userB) AND ending is null", [
			":userA" => $this->speakerA_id,
			":userB" => $this->speakerB_id,
		]);
	}

	public function getSociety() {
		return $this->hasOne(Society::className(), ['id' => 'society_id']);
	}

	/**
	 * Get the points the team is on after the specified round.
	 *
	 * @param integer $number
	 *
	 * @return int
	 */
	public function getPointsAfterRound($number) {

		$points = 0;

		for ($i = 1; $i <= $number; $i++) {
			$debateQuery = Debate::find()->leftJoin("round", "round.id = debate.round_id")->where([
				"round.number" => $i,
				"round.tournament_id" => $this->tournament_id
			]);

			$debateQuery->andWhere(["og_team_id" => $this->id]);
			$debateQuery->orWhere(["oo_team_id" => $this->id]);
			$debateQuery->orWhere(["cg_team_id" => $this->id]);
			$debateQuery->orWhere(["co_team_id" => $this->id]);

			$debate = $debateQuery->one();


			if ($debate instanceof Debate && $debate->result instanceof Result) {
				foreach (Debate::positions() as $p) {
					if ($debate->{$p . "_team_id"} == $this->id)
						$position = $p;
				}

				$points += (4 - $debate->result->{$position . "_place"});

			}
		}
		return $points;
	}

	public function getDebates() {
		return Debate::findBySql("SELECT * FROM debate WHERE "
			. "(og_team_id = :teamid "
			. "OR oo_team_id = :teamid "
			. "OR cg_team_id= :teamid "
			. "OR co_team_id = :teamid) "
			. "AND tournament_id = :tournamentid", [
			":teamid" => $this->id,
			":tournamentid" => $this->tournament_id,
		]);
	}

	public function getDebate($roundid) {
		return $this->getDebates()->andWhere(["round_id" => $roundid]);
	}

	/**
	 * Sort comparison function based on team points
	 *
	 * @param Team $a
	 * @param Team $b
	 */
	public static function compare_points($a, $b) {
		$ap = $a["points"];
		$bp = $b["points"];
		return ($ap < $bp) ? 1 : (($ap > $bp) ? -1 : 0);
	}

	/**
	 * Helper function to determine whether teams COULD replace each other in the same bracket (are they in the same
	 * bracket, or is one a pull up / down from their bracket?) Debate level = hightest points of teams
	 *
	 * @param Team $other_team
	 *
	 * @uses Team::getPoints
	 * @uses Team::getLevel
	 * @return bool
	 */
	public static function is_swappable_with($team, $other_team, $line_a_level, $line_b_level) {
		$result = ($team["id"] != $other_team["id"]) &&
			(($team["points"] == $other_team["points"]) ||
				($line_a_level == $line_b_level));
		return $result;
	}

	/**
	 * The Position Badness Lookup table
	 *
	 * @todo make that dynamic, bitch!
	 * @return array
	 */
	public static function PositionBadnessTable() {
		return array(
			"0, 0, 0, 0" => 0,
			"0, 0, 0, 1" => 0,
			"0, 0, 0, 2" => 4,
			"0, 0, 0, 3" => 36,
			"0, 0, 0, 4" => 144,
			"0, 0, 0, 5" => 324,
			"0, 0, 0, 6" => 676,
			"0, 0, 0, 7" => 1296,
			"0, 0, 0, 8" => 2304,
			"0, 0, 0, 9" => 3600,
			"0, 0, 1, 1" => 0,
			"0, 0, 1, 2" => 4,
			"0, 0, 1, 3" => 36,
			"0, 0, 1, 4" => 100,
			"0, 0, 1, 5" => 256,
			"0, 0, 1, 6" => 576,
			"0, 0, 1, 7" => 1156,
			"0, 0, 1, 8" => 1936,
			"0, 0, 2, 2" => 16,
			"0, 0, 2, 3" => 36,
			"0, 0, 2, 4" => 100,
			"0, 0, 2, 5" => 256,
			"0, 0, 2, 6" => 576,
			"0, 0, 2, 7" => 1024,
			"0, 0, 3, 3" => 64,
			"0, 0, 3, 4" => 144,
			"0, 0, 3, 5" => 324,
			"0, 0, 3, 6" => 576,
			"0, 0, 4, 4" => 256,
			"0, 0, 4, 5" => 400,
			"0, 1, 1, 1" => 0,
			"0, 1, 1, 2" => 4,
			"0, 1, 1, 3" => 16,
			"0, 1, 1, 4" => 64,
			"0, 1, 1, 5" => 196,
			"0, 1, 1, 6" => 484,
			"0, 1, 1, 7" => 900,
			"0, 1, 2, 2" => 4,
			"0, 1, 2, 3" => 16,
			"0, 1, 2, 4" => 64,
			"0, 1, 2, 5" => 196,
			"0, 1, 2, 6" => 400,
			"0, 1, 3, 3" => 36,
			"0, 1, 3, 4" => 100,
			"0, 1, 3, 5" => 196,
			"0, 1, 4, 4" => 144,
			"0, 2, 2, 2" => 4,
			"0, 2, 2, 3" => 16,
			"0, 2, 2, 4" => 64,
			"0, 2, 2, 5" => 144,
			"0, 2, 3, 3" => 36,
			"0, 2, 3, 4" => 64,
			"0, 3, 3, 3" => 36,
			"1, 1, 1, 1" => 0,
			"1, 1, 1, 2" => 0,
			"1, 1, 1, 3" => 4,
			"1, 1, 1, 4" => 36,
			"1, 1, 1, 5" => 144,
			"1, 1, 1, 6" => 324,
			"1, 1, 2, 2" => 0,
			"1, 1, 2, 3" => 4,
			"1, 1, 2, 4" => 36,
			"1, 1, 2, 5" => 100,
			"1, 1, 3, 3" => 16,
			"1, 1, 3, 4" => 36,
			"1, 2, 2, 2" => 0,
			"1, 2, 2, 3" => 4,
			"1, 2, 2, 4" => 16,
			"1, 2, 3, 3" => 4,
			"2, 2, 2, 2" => 0,
			"2, 2, 2, 3" => 0
		);
	}

	/**
	 * Gets an integer value representing how BAD the current position is for the Team

	 *
*@param integer $pos
	 * @param Array $team
	 *
*@return integer
	 */
	public static function getPositionBadness($pos, $team) {

		$positions = $team["positionMatrix"];
		$badness_lookup = Team::PositionBadnessTable();

		$positions[$pos] += 1;
		sort($positions);

		while (($positions[0] + $positions[1] + $positions[2] + $positions[3]) >= 10) {
			for ($i = 0; $i < 4; $i++)
				$positions[$i] = max(0, $positions[$i] - 1);
		}
		return $badness_lookup["{$positions[0]}, {$positions[1]}, {$positions[2]}, {$positions[3]}"];
	}

	/**
	 * Return the previous PositionMatrix the Team has been in to
	 * 0 => OG,
	 * 1 => OO,
	 * 2 => CG,
	 * 3 => CO,
	 *
	 * @return array[4]
	 */
	public static function getPastPositionMatrix($id, $tournament_id) {

		$og = Debate::find()->where(["tournament_id" => $tournament_id, "og_team_id" => $id])->count();
		$oo = Debate::find()->where(["tournament_id" => $tournament_id, "oo_team_id" => $id])->count();
		$cg = Debate::find()->where(["tournament_id" => $tournament_id, "cg_team_id" => $id])->count();
		$co = Debate::find()->where(["tournament_id" => $tournament_id, "co_team_id" => $id])->count();

		return [$og, $oo, $cg, $co];
	}

}
