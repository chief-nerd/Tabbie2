<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "source_message".
 *
 * @property integer   $id
 * @property string    $category
 * @property string    $message
 *
 * @property Message[] $messages
 */
class SourceMessage extends \yii\db\ActiveRecord {

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'source_message';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['message'], 'string'],
			[['category'], 'string', 'max' => 32]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id'       => Yii::t('app', 'ID'),
			'category' => Yii::t('app', 'Category'),
			'message'  => Yii::t('app', 'Message'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTranslations() {
		return $this->hasMany(Message::className(), ['id' => 'id']);
	}
}
