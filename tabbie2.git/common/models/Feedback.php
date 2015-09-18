<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "feedback".
 *
 * @property integer             $id
 * @property integer             $debate_id
 * @property string              $time
 * @property integer             $to_type
 * @property integer             $to_id
 * @property Debate              $debate
 * @property FeedbackHasAnswer[] $feedbackHasAnswers
 * @property Answer[]            $answers
 */
class Feedback extends \yii\db\ActiveRecord
{

	const FROM_CHAIR = 1;
	const FROM_WING = 2;
	const FROM_TEAM = 3;

	const TO_CHAIR = 1;
	const TO_WING = 2;

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'feedback';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['debate_id'], 'required'],
			[['debate_id', 'to_type', 'to_id'], 'integer'],
			[['time'], 'safe']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'        => Yii::t('app', 'ID'),
			'debate_id' => Yii::t('app', 'Debate') . ' ' . Yii::t('app', 'ID'),
			'time'      => Yii::t('app', 'Time'),
			'to_type' => Yii::t('app', 'Type'),
			'to_id'     => Yii::t('app', 'Feedback To ID'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getDebate()
	{
		return $this->hasOne(Debate::className(), ['id' => 'debate_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getAnswers()
	{
		return $this->hasMany(Answer::className(), ['feedback_id' => 'id']);
	}

	public function getTo()
	{
		switch ($this->to_type) {
			case self::TO_CHAIR:
			case self::TO_WING:
				return Adjudicator::find()->where(["id" => $this->to_id]);
		}
	}
}
