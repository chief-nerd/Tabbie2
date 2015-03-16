<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "answer".
 *
 * @property integer             $id
 * @property integer             $questions_id
 * @property string              $value
 * @property Questions           $questions
 * @property FeedbackHasAnswer[] $feedbackHasAnswers
 * @property Feedback[]          $feedbacks
 */
class Answer extends \yii\db\ActiveRecord {
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'answer';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['questions_id'], 'required'],
			[['questions_id'], 'integer'],
			[['value'], 'string']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('app', 'ID'),
			'questions_id' => Yii::t('app', 'Questions ID'),
			'value' => Yii::t('app', 'Value'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getQuestions() {
		return $this->hasOne(Questions::className(), ['id' => 'questions_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getFeedbackHasAnswers() {
		return $this->hasMany(FeedbackHasAnswer::className(), ['answer_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getFeedbacks() {
		return $this->hasMany(Feedback::className(), ['id' => 'feedback_id'])
		            ->viaTable('feedback_has_answer', ['answer_id' => 'id']);
	}
}
