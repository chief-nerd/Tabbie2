<?php

namespace common\models;

use JmesPath\Tests\_TestJsonStringClass;
use kartik\widgets\StarRating;
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
 * @property string                   $param
 * @property Answer[]                 $answers
 * @property TournamentHasQuestions[] $tournamentHasQuestions
 * @property Tournament[]             $tournaments
 */
class Question extends \yii\db\ActiveRecord
{

	const TYPE_INPUT = 1;
	const TYPE_STAR = 0;
	const TYPE_TEXT = 2;
	const TYPE_NUMBER = 3;
	const TYPE_CHECKBOX = 4;

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'question';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['text', 'type'], 'required'],
			[['type', 'apply_T2C', 'apply_C2W', 'apply_W2C'], 'integer'],
			[['text', 'param'], 'string', 'max' => 255]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'        => Yii::t('app', 'ID'),
			'text'      => Yii::t('app', 'Text'),
			'type'      => Yii::t('app', 'Type'),
			'param' => Yii::t('app', 'Parameter if needed'),
			'apply_T2C' => Yii::t('app', 'Apply to Team -> Chair'),
			'apply_C2W' => Yii::t('app', 'Apply to Chair -> Wing'),
			'apply_W2C' => Yii::t('app', 'Apply to Wing -> Chair'),
		];
	}

	public static function starLabels($id = null) {
		$table = [
			0 => Yii::t("app", "Not Rated"),
			1 => Yii::t("app", "Not Good"),
			2 => Yii::t("app", "Decent"),
			3 => Yii::t("app", "Good"),
			4 => Yii::t("app", "Very Good"),
			5 => Yii::t("app", "Excellent"),
		];

		return ($id !== null) ? $table[$id] : $table;
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getAnswers()
	{
		return $this->hasMany(Answer::className(), ['questions_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTournamentHasQuestion()
	{
		return $this->hasMany(TournamentHasQuestion::className(), ['questions_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTournaments()
	{
		return $this->hasMany(Tournament::className(), ['id' => 'tournament_id'])
			->viaTable('tournament_has_questions', ['questions_id' => 'id']);
	}

	public function getTypeOptions($id = null)
	{
		$types = [
			self::TYPE_STAR   => Yii::t("app", "Star Rating (1-5) Field"),
			self::TYPE_INPUT  => Yii::t("app", "Short Text Field"),
			self::TYPE_TEXT   => Yii::t("app", "Long Text Field"),
			self::TYPE_NUMBER => Yii::t("app", "Number Field"),
			self::TYPE_CHECKBOX => Yii::t("app", "Checkbox List Field"),
		];

		return ($id === null) ? $types : $types[$id];
	}

	public function getParam()
	{
		return json_decode($this->param);
	}

	public function setParam($param)
	{
		if (!is_array($param)) {
			$param = $param;
		}
		$this->param = json_encode($param);
	}

}
