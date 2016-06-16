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
 * @property integer $from_id
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
	const TO_CHAIR_FROM_TEAM = 3;

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

	/**
	 * Get the TO object that the feedback is refering to
	 * @return Adjudicator $this
	 */
	public function getTo()
	{
		//Feedback is always on an Adjudicator
		return Adjudicator::find()->where(["id" => $this->to_id]);
	}

	public function getType_To_String()
	{
		switch ($this->to_type) {
			case Feedback::TO_CHAIR_FROM_TEAM:
			case Feedback::TO_CHAIR:
				return Yii::t("app", "Chair");
			case Feedback::TO_WING:
				return Yii::t("app", "Wing");
		}
	}

	public function getType_From_String()
	{
		switch ($this->to_type) {
			case Feedback::TO_CHAIR:
				return Yii::t("app", "Wing");
			case Feedback::TO_WING:
				return Yii::t("app", "Chair");
			case Feedback::TO_CHAIR_FROM_TEAM:
				return Yii::t("app", "Team");
		}
	}

	/**
	 * Get the object the feedback was given from
	 * @return Adjudicator|Team $this
	 */
	public function getFrom()
	{
		switch ($this->to_type) {
			case self::TO_CHAIR:
			case self::TO_WING:
				return Adjudicator::find()->where(["id" => $this->from_id]);
			case self::TO_CHAIR_FROM_TEAM:
				return Team::find()->where(["id" => $this->from_id]);
		}
	}
}
