<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "team_strike".

 *
*@property integer     $id
 * @property integer     $team_id
 * @property integer     $adjudicator_id
 * @property integer     $tournament_id
 * @property Team        $team
 * @property Adjudicator $adjudicator
 */
class TeamStrike extends \yii\db\ActiveRecord {
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'team_strike';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['team_id', 'adjudicator_id'], 'required'],
			[['team_id', 'adjudicator_id'], 'integer'],
			['tournament_id', 'safe']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('app', 'ID'),
			'team_id' => Yii::t('app', 'Team'),
			'adjudicator_id' => Yii::t('app', 'Adjudicator'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTeam() {
		return $this->hasOne(Team::className(), ['id' => 'team_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getAdjudicator() {
		return $this->hasOne(Adjudicator::className(), ['id' => 'adjudicator_id']);
	}
}
