<?php

namespace common\models;

use kartik\rating\StarRating;
use Yii;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * This is the model class for table "answer".
 *
 * @property integer    $id
 * @property integer    $question_id
 * @property integer    $feedback_id
 * @property string     $value
 * @property Question   $question
 * @property Feedback[] $feedback
 */
class Answer extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'answer';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['question_id', 'feedback_id'], 'required'],
			[['question_id', 'feedback_id'], 'integer'],
			[['value'], 'string']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'          => Yii::t('app', 'ID'),
			'feedback_id' => Yii::t('app', 'Feedback ID'),
			'question_id' => Yii::t('app', 'Question ID'),
			'value'       => Yii::t('app', 'Value'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getQuestion()
	{
		return $this->hasOne(Question::className(), ['id' => 'question_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getFeedback()
	{
		return $this->hasOne(Feedback::className(), ['id' => 'feedback_id']);
	}

	public function getName($q_id)
	{
		return "Answer[" . $q_id . "]";
	}

	public function renderLabel($q_id)
	{
		return '<label class="control-label" for="' . Html::encode($this->getName($q_id)) . '">' . Html::encode($this->question->text) . '</label>';
	}

	/**
	 * @param ActiveForm $form
	 *
	 * @return string
	 */
	public function renderField($q_id)
	{
		//<input id="answer-value" class="form-control" name="Answer[value]" type="text">
		$element = null;
		switch ($this->question->type) {
			case Question::TYPE_INPUT:
				$element = Html::textInput($this->getName($q_id), $this->value, [
					"class" => "form-control",
					"name"  => "Answer[" . $q_id . "]",
				]);
				break;
			case Question::TYPE_TEXT:
				$element = Html::textarea($this->getName($q_id), $this->value, [
					"class" => "form-control",
					"name"  => "Answer[" . $q_id . "]",
				]);
				break;
			case Question::TYPE_STAR:
				$element = StarRating::widget([
					"name"          => "Answer[" . $q_id . "]",
					"id"            => "Answer_" . $q_id,
					"pluginOptions" => [
						"stars" => 5,
						"min"   => 0,
						"max"   => 5,
						"step"  => 1,
						"size"  => "md",
					],
				]);
				break;
		}

		return $element;
	}
}
