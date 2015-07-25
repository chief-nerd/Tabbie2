<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "adjudicator_strike".
 *
 * @property integer     $adjudicator_id
 * @property integer     $adjudicator_id1
 * @property integer     $tournament_id
 * @property Adjudicator $adjudicator
 * @property Adjudicator $adjudicatorId1
 */
class AdjudicatorStrike extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'adjudicator_strike';
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
	public function rules()
	{
		return [
			[['adjudicator_to_id', 'adjudicator_from_id'], 'required'],
			[['adjudicator_to_id', 'adjudicator_from_id'], 'integer'],
			['tournament_id', 'safe']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'adjudicator_from_id' => Yii::t('app', 'Adjudicator From ID'),
			'adjudicator_to_id'   => Yii::t('app', 'Adjudicator To ID'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getAdjudicatorFrom()
	{
		return $this->hasOne(Adjudicator::className(), ['id' => 'adjudicator_from_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getAdjudicatorTo()
	{
		return $this->hasOne(Adjudicator::className(), ['id' => 'adjudicator_to_id']);
	}
}
