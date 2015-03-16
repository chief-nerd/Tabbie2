<?php

namespace common\models;

use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "question".
 *
 * @property integer                  $id
 * @property string                   $text
 * @property integer                  $type
 * @property integer                  $apply_T2C
 * @property integer                  $apply_C2W
 * @property integer                  $apply_W2C
 * @property Answer[]                 $answers
 * @property TournamentHasQuestions[] $tournamentHasQuestions
 * @property Tournament[]             $tournaments
 */
class Question extends \yii\db\ActiveRecord {

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'question';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['text', 'type'], 'required'],
			[['type', 'apply_T2C', 'apply_C2W', 'apply_W2C'], 'integer'],
			[['text'], 'string', 'max' => 255]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('app', 'ID'),
			'text' => Yii::t('app', 'Text'),
			'type' => Yii::t('app', 'Type'),
			'apply_T2C' => Yii::t('app', 'Apply to Team -> Chair'),
			'apply_C2W' => Yii::t('app', 'Apply to Chair -> Wing'),
			'apply_W2C' => Yii::t('app', 'Apply to Wing -> Chair'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getAnswers() {
		return $this->hasMany(Answer::className(), ['questions_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTournamentHasQuestion() {
		return $this->hasMany(TournamentHasQuestion::className(), ['questions_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTournaments() {
		return $this->hasMany(Tournament::className(), ['id' => 'tournament_id'])
		            ->viaTable('tournament_has_questions', ['questions_id' => 'id']);
	}

	public function getTypeOptions($id = null) {
		$types = [
			0 => "Star Rating (1-5)",
			1 => "Short Text",
			2 => "Long Text",
			3 => "Number",
		];
		return ($id === null) ? $types : $types[$id];
	}

	public function getName() {
		return "Question[" . $this->id . "]";
	}

	public function renderLabel() {
		return Html::label($this->text, $this->name);
	}

	public function renderInput() {
		$element = null;
		switch ($this->type) {
			case 0:
				$element = Html::textInput($this->name, (($this->answers instanceof \common\models\Answer) ? $this->answers->value : ''));
				break;
			case 1:
				$element = Html::textarea($this->name);
				break;
			case 2:
				$element = \kartik\widgets\StarRating::widget();
				break;
		}
		return $element;
	}

}
