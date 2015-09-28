<?php

namespace backend\models;

use common\models\Language;
use common\models\User;
use Yii;

/**
 * This is the model class for table "language_maintainer".
 *
 * @property integer $user_id
 * @property string $language_language
 *
 * @property User $user
 * @property Language $languageLanguage
 */
class LanguageMaintainer extends \yii\db\ActiveRecord {

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'language_maintainer';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['user_id', 'language_language'], 'required'],
			[['user_id'], 'integer'],
			[['language_language'], 'string', 'max' => 16]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'user_id' => Yii::t('app', 'User ID'),
			'language_language' => Yii::t('app', 'Language Code'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser() {
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getLanguage() {
		return $this->hasOne(Language::className(), ['language' => 'language_language']);
	}
}
