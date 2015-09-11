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

	public $csvFile;
	public $tempImport;
	public $delimiter = self::DEL_COMMA;
	public $is_test;

	public static function getDelimiterOptions() {
		return [
			self::DEL_COMMA   => Yii::t("app", "Comma ( , ) separated file"),
			self::DEL_SEMICOL => Yii::t("app", "Semicolon ( ; ) separated file"),
			self::DEL_TAB     => Yii::t("app", "Tab ( \\t ) separated file")
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			// name, email, subject and body are required
			[['csvFile'], 'required'],
			[['delimiter'], 'string', 'max' => 1],
			[['is_test'], 'integer']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'csvFile' => Yii::t("app", '*.csv File'),
			'delimiter' => Yii::t("app", 'Delimiter'),
			'is_test' => Yii::t("app", 'Mark as Test Data Import (prohibits Email sending)'),
		];
	}

}
