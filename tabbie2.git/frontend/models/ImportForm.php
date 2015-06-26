<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ImportForm extends Model {

	public $csvFile;
	public $tempImport;
	public $is_test;

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			// name, email, subject and body are required
			[['csvFile'], 'required'],
			[['is_test'], 'integer']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'csvFile' => Yii::t("app", '*.csv File'),
			'is_test' => Yii::t("app", 'Mark as Test Data Import (prohibits Email sending)'),
		];
	}

}
