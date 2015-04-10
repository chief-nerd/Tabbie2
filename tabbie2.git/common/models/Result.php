<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "result".
 *
 * @property integer     $id
 * @property integer     $debate_id
 * @property integer     $og_A_speaks
 * @property integer     $og_B_speaks
 * @property integer     $og_place
 * @property integer     $oo_A_speaks
 * @property integer     $oo_B_speaks
 * @property integer     $oo_place
 * @property integer     $cg_A_speaks
 * @property integer     $cg_B_speaks
 * @property integer     $cg_place
 * @property integer     $co_A_speaks
 * @property integer     $co_B_speaks
 * @property integer     $co_place
 * @property string      $time
 * @property integer     $entered_by_id
 * @property Debate      $debate
 * @property Adjudicator $enteredByAdjudicator
 */
class Result extends \yii\db\ActiveRecord {

	public $confirmed;

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'result';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['debate_id', 'og_A_speaks', 'og_B_speaks', 'og_place', 'oo_A_speaks', 'oo_B_speaks', 'oo_place', 'cg_A_speaks', 'cg_B_speaks', 'cg_place', 'co_A_speaks', 'co_B_speaks', 'co_place', 'entered_by_id'], 'required'],
			[['debate_id', 'og_place', 'oo_place', 'cg_place', 'co_place', 'entered_by_id'], 'integer'],
			[['og_A_speaks', 'og_B_speaks', 'oo_A_speaks', 'oo_B_speaks', 'cg_A_speaks', 'cg_B_speaks', 'co_A_speaks', 'co_B_speaks'],
				"integer", "max" => Yii::$app->params["speaks_max"], "min" => Yii::$app->params["speaks_min"]],
			['debate_id', 'validateNotEqualPlace'],
			['debate_id', 'unique'],
			[['time', 'confirmed'], 'safe']
		];
	}

	/**
	 * Checks that every team speaks count is unique.
	 * Prevents same position.
	 *
	 * @param type $attribute
	 * @param type $params
	 */
	public function validateNotEqualPlace($attribute, $params) {
		$positions = ["og", "oo", "cg", "co"];
		$results = [
			"og" => $this->og_speaks,
			"oo" => $this->oo_speaks,
			"cg" => $this->cg_speaks,
			"co" => $this->co_speaks,
		];

		$results = array_unique($results);

		foreach ($positions as $pos) {
			if (!array_key_exists($pos, $results)) {
				$this->addError($pos . "_A_speaks", 'Equal place exist');
				$this->addError($pos . "_B_speaks", 'Equal place exist');
			}
		}
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('app', 'ID'),
			'debate_id' => Yii::t('app', 'Debate ID'),
			'og_A_speaks' => Yii::t('app', 'OG A Speaks'),
			'og_B_speaks' => Yii::t('app', 'OG B Speaks'),
			'og_place' => Yii::t('app', 'OG Place'),
			'oo_A_speaks' => Yii::t('app', 'OO A Speaks'),
			'oo_B_speaks' => Yii::t('app', 'OO B Speaks'),
			'oo_place' => Yii::t('app', 'OO Place'),
			'cg_A_speaks' => Yii::t('app', 'CG A Speaks'),
			'cg_B_speaks' => Yii::t('app', 'CG B Speaks'),
			'cg_place' => Yii::t('app', 'CG Place'),
			'co_A_speaks' => Yii::t('app', 'CO A Speaks'),
			'co_B_speaks' => Yii::t('app', 'CO B Speaks'),
			'co_place' => Yii::t('app', 'CO Place'),
			'time' => Yii::t('app', 'Time'),
			'entered_by_id' => Yii::t('app', 'Entered by User ID'),
		];
	}

	public function getOg_speaks() {
		return $this->og_A_speaks + $this->og_B_speaks;
	}

	public function getOo_speaks() {
		return $this->oo_A_speaks + $this->oo_B_speaks;
	}

	public function getCg_speaks() {
		return $this->cg_A_speaks + $this->cg_B_speaks;
	}

	public function getCo_speaks() {
		return $this->co_A_speaks + $this->co_B_speaks;
	}

	public function rankTeams() {
		$results = [
			"og" => $this->og_speaks,
			"oo" => $this->oo_speaks,
			"cg" => $this->cg_speaks,
			"co" => $this->co_speaks,
		];
		asort($results, SORT_NUMERIC);
		$results = array_reverse($results);
		$keys = array_keys($results);
		for ($i = 0; $i < count($results); $i++) {
			$this->{$keys[$i] . "_place"} = ($i + 1);
		}
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getDebate() {
		return $this->hasOne(Debate::className(), ['id' => 'debate_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getEnteredByUser() {
		return $this->hasOne(User::className(), ['id' => 'entered_by_id']);
	}

	public function updateTeamCache() {
		foreach ($this->debate->getTeams() as $pos => $team) {
			/** @var Team $team */
			$team->points += (4 - $this->{$pos . "_place"});
			$team->speakerA_speaks += $this->{$pos . "_A_speaks"};
			$team->speakerB_speaks += $this->{$pos . "_B_speaks"};
			$team->save();
		}
	}

}
