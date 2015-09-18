<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ImportForm extends Model
{

	const DEL_COMMA = ',';
	const DEL_SEMICOL = ';';
	const DEL_TAB = "\t";

	const DEL_O_COMMA = 1;
	const DEL_O_SEMICOL = 2;
	const DEL_O_TAB = 3;

	public $csvFile;
	public $tempImport;
	public $delimiter;
	public $is_test;

	public static function getDelimiterOptions() {
		return [
			self::DEL_O_COMMA   => Yii::t("app", "Comma ( , ) separated file"),
			self::DEL_O_SEMICOL => Yii::t("app", "Semicolon ( ; ) separated file"),
			self::DEL_O_TAB => Yii::t("app", "Tab ( ->| ) separated file")
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['csvFile'], 'required'],
			[['is_test'], 'integer'],
			[['delimiter', 'is_test'], 'safe']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'csvFile' => Yii::t("app", 'CSV File'),
			'delimiter' => Yii::t("app", 'Delimiter'),
			'is_test'   => Yii::t("app", 'Mark as Test Data Import (prohibits Email sending)'),
		];
	}

	public function getDelimiterChar()
	{
		return self::option2char($this->delimiter);
	}

	public static function option2char($option) {
		$chars = [
			self::DEL_O_COMMA   => self::DEL_COMMA,
			self::DEL_O_SEMICOL => self::DEL_SEMICOL,
			self::DEL_O_TAB     => self::DEL_TAB,
		];

		return $chars[$option];
	}

}
