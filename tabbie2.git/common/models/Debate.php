<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "debate".
 *
 * @property integer    $id
 * @property integer    $round_id
 * @property integer    $tournament_id
 * @property integer    $og_team_id
 * @property integer    $oo_team_id
 * @property integer    $cg_team_id
 * @property integer    $co_team_id
 * @property integer    $panel_id
 * @property integer    $venue_id
 * @property int        $energy
 * @property integer    $og_feedback
 * @property integer    $oo_feedback
 * @property integer    $cg_feedback
 * @property integer    $co_feedback
 * @property string     $time
 * @property string     $messages
 * @property Panel      $panel
 * @property Team       $og_team
 * @property Team       $oo_team
 * @property Team       $cg_team
 * @property Team       $co_team
 * @property Venue      $venue
 * @property Feedback[] $feedbacks
 * @property Result     $result
 * @property Tournament $tournament
 * @property Round      $round
 */
class Debate extends \yii\db\ActiveRecord
{

	public $draw_sort = "";

	public static function findOneByChair($user_id, $tournament_id, $round_id)
	{
		$query = static::find();
		$query->sql = "SELECT debate.* FROM " . Adjudicator::tableName() . " "
			. "LEFT JOIN " . AdjudicatorInPanel::tableName() . " ON " . Adjudicator::tableName() . ".id = adjudicator_id "
			. "LEFT JOIN " . Panel::tableName() . " ON panel_id = " . Panel::tableName() . ".id "
			. "LEFT JOIN " . Debate::tableName() . " ON " . Debate::tableName() . ".panel_id = " . Panel::tableName() . ".id "
			. "WHERE user_id = :user_id "
			. "AND FUNCTION = " . Panel::FUNCTION_CHAIR . " "
			. "AND round_id = :round_id "
			. "AND debate.tournament_id = :tournament_id";

		$params = [
			":user_id"       => $user_id,
			":round_id"      => $round_id,
			":tournament_id" => $tournament_id,
		];

		return $query->params($params)->one();
	}

	/**
	 * @inheritdoc
	 * @return TournamentQuery
	 */
	public static function find()
	{
		return new TournamentQuery(get_called_class());
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'debate';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['round_id', 'tournament_id', 'og_team_id', 'oo_team_id', 'cg_team_id', 'co_team_id', 'panel_id', 'venue_id'], 'required'],
			[['round_id', 'tournament_id', 'og_team_id', 'oo_team_id', 'cg_team_id', 'co_team_id', 'panel_id', 'venue_id', 'energy', 'og_feedback', 'oo_feedback', 'cg_feedback', 'co_feedback'], 'integer'],
			['messages', "string"],
			[['time'], 'safe']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'            => Yii::t('app', 'ID'),
			'round_id'      => Yii::t('app', 'Round') . ' ' . Yii::t('app', 'ID'),
			'tournament_id' => Yii::t('app', 'Tournament') . ' ' . Yii::t('app', 'ID'),
			'og_team_id'    => Yii::t('app', 'OG Team') . ' ' . Yii::t('app', 'ID'),
			'oo_team_id'    => Yii::t('app', 'OO Team') . ' ' . Yii::t('app', 'ID'),
			'cg_team_id'    => Yii::t('app', 'CG Team') . ' ' . Yii::t('app', 'ID'),
			'co_team_id'    => Yii::t('app', 'CO Team') . ' ' . Yii::t('app', 'ID'),
			'panel_id'      => Yii::t('app', 'Panel') . ' ' . Yii::t('app', 'ID'),
			'venue_id'      => Yii::t('app', 'Venue') . ' ' . Yii::t('app', 'ID'),
			'og_feedback'   => Yii::t('app', 'OG Feedback'),
			'oo_feedback'   => Yii::t('app', 'OO Feedback'),
			'cg_feedback'   => Yii::t('app', 'CG Feedback'),
			'co_feedback'   => Yii::t('app', 'CO Feedback'),
			'time'          => Yii::t('app', 'Time'),
			'messages'      => Yii::t('app', 'Messages')
		];
	}

	public function getLanguage_status()
	{
		if (!isset($this->og_team) ||
			!isset($this->oo_team) ||
			!isset($this->cg_team) ||
			!isset($this->co_team)
		)
			return User::LANGUAGE_NONE;

		//Highest Status
		$status = max([
			$this->og_team->language_status,
			$this->oo_team->language_status,
			$this->cg_team->language_status,
			$this->co_team->language_status,
		]);
		//Look if Equal
		if ($status == $this->og_team->language_status &&
			$status == $this->oo_team->language_status &&
			$status == $this->cg_team->language_status &&
			$status == $this->cg_team->language_status
		) {
			if ($status == 0)
				$status = 1;

			return $status;
		} else
			return -1;

	}

	public function getHighestPoints()
	{
		if (!isset($this->og_team) ||
			!isset($this->oo_team) ||
			!isset($this->cg_team) ||
			!isset($this->co_team)
		)
			return 0;

		return max([
			$this->og_team->points,
			$this->oo_team->points,
			$this->cg_team->points,
			$this->co_team->points,
		]);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPanel()
	{
		return $this->hasOne(Panel::className(), ['id' => 'panel_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getVenue()
	{
		return $this->hasOne(Venue::className(), ['id' => 'venue_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTournament()
	{
		return $this->hasOne(Tournament::className(), ['id' => 'tournament_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getFeedbacks()
	{
		return $this->hasMany(Feedback::className(), ['debate_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getResult()
	{
		return $this->hasOne(Result::className(), ['debate_id' => 'id']);
	}

	public function getOg_team()
	{
		return $this->hasOne(Team::className(), ['id' => 'og_team_id']);
	}

	public function getOo_team()
	{
		return $this->hasOne(Team::className(), ['id' => 'oo_team_id']);
	}

	public function getCg_team()
	{
		return $this->hasOne(Team::className(), ['id' => 'cg_team_id']);
	}

	public function getCo_team()
	{
		return $this->hasOne(Team::className(), ['id' => 'co_team_id']);
	}

	public function getRound()
	{
		return $this->hasOne(Round::className(), ['id' => 'round_id']);
	}

	public function getAdjudicators()
	{
		return $this->hasMany(Adjudicator::className(), ["id" => "adjudicator_id"])
			->viaTable("panel", ["panel.id" => "panel_id"])
			->viaTable('adjudicator_in_panel', ['panel_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getAdjudicatorsInPanel() {
		return $this->hasMany(AdjudicatorInPanel::className(), ["panel_id" => "id"])
			->viaTable("panel", ["id" => "panel_id"]);
	}

	/**
	 * @deprecated
	 * @return \yii\db\ActiveQuery
	 */
	public function getAdjudicatorObjects()
	{
		return Adjudicator::findBySql("SELECT adjudicator.* FROM " . Adjudicator::tableName() . " "
			. "LEFT OUTER JOIN " . AdjudicatorInPanel::tableName() . " ON " . Adjudicator::tableName() . ".id = " . AdjudicatorInPanel::tableName() . ".adjudicator_id "
			. "LEFT OUTER JOIN " . Panel::tableName() . " ON panel_id = " . Panel::tableName() . ".id "
			. "LEFT OUTER JOIN " . Debate::tableName() . " ON " . Debate::tableName() . ".panel_id = " . Panel::tableName() . ".id "
			. "WHERE " . Debate::tableName() . ".id = " . $this->id);
	}

	/**
	 * @return mixed
	 */
	public function getChair()
	{
		return Adjudicator::find()->joinWith("panels")->leftJoin("debate", "debate.panel_id = panel.id")->where([
			"debate.id"                     => $this->id,
			"adjudicator_in_panel.function" => Panel::FUNCTION_CHAIR,
		])->one();
	}

	public function isOGTeamMember($id)
	{
		if ($this->og_team->speakerA_id == $id || $this->og_team->speakerB_id == $id)
			return true;
		else
			return false;
	}

	public function isOOTeamMember($id)
	{
		if ($this->oo_team->speakerA_id == $id || $this->oo_team->speakerB_id == $id)
			return true;
		else
			return false;
	}

	public function isCGTeamMember($id)
	{
		if ($this->cg_team->speakerA_id == $id || $this->cg_team->speakerB_id == $id)
			return true;
		else
			return false;
	}

	public function isCOTeamMember($id)
	{
		if ($this->co_team->speakerA_id == $id || $this->co_team->speakerB_id == $id)
			return true;
		else
			return false;
	}

	/**
	 * Get the Teams in a searchable Array
	 *
	 * @param bool $onlyKeys
	 *
	 * @return Team[]|array
	 */
	public function getTeams($onlyKeys = false)
	{
		return [
			"og" => ($onlyKeys) ? $this->og_team_id : $this->og_team,
			"oo" => ($onlyKeys) ? $this->oo_team_id : $this->oo_team,
			"cg" => ($onlyKeys) ? $this->cg_team_id : $this->cg_team,
			"co" => ($onlyKeys) ? $this->co_team_id : $this->co_team,
		];
	}

	public function setMessages($array)
	{
		ksort($array);
		$this->messages = json_encode($array);
	}

	public function getMessages()
	{
		return ($this->messages) ? json_decode($this->messages) : [];
	}

}
