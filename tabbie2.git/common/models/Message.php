<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "message".
 *
 * @property integer       $id
 * @property string        $language
 * @property string        $translation
 *
 * @property SourceMessage $id0
 * @property Language      $language0
 */
class Message extends \yii\db\ActiveRecord {

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'message';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['id', 'language'], 'required'],
			[['id'], 'integer'],
			[['translation'], 'string'],
			[['language'], 'string', 'max' => 16]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id'          => Yii::t('app', 'ID'),
			'language'    => Yii::t('app', 'Language'),
			'translation' => Yii::t('app', 'Translation'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getOriginal() {
		return $this->hasOne(SourceMessage::className(), ['id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getLanguageObject() {
		return $this->hasOne(Language::className(), ['language' => 'language']);
	}
}
