<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "adjudicator_strike".
 *
 * @property integer     $adjudicator_from_id
 * @property integer     $adjudicator_to_id
 * @property integer     $tournament_id
 * @property bool        $accepted
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
			[['adjudicator_to_id', 'adjudicator_from_id', 'accepted'], 'integer'],
			[['tournament_id', 'accepted'], 'safe']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'adjudicator_from_id' => Yii::t('app', 'Adjudicator Strike From ID'),
			'adjudicator_to_id'   => Yii::t('app', 'Adjudicator Strike To ID'),
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
