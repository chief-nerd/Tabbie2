<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "feedback".
 *
 * @property integer             $id
 * @property integer             $debate_id
 * @property string              $time
 * @property Debate              $debate
 * @property FeedbackHasAnswer[] $feedbackHasAnswers
 * @property Answer[]            $answers
 */
class Feedback extends \yii\db\ActiveRecord
{

	const FROM_CHAIR = 1;
	const FROM_WING = 2;
	const FROM_TEAM = 3;

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
			[['debate_id'], 'integer'],
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
			'debate_id' => Yii::t('app', 'Debate ID'),
			'time'      => Yii::t('app', 'Time'),
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
}
