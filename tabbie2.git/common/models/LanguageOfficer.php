<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "language_officer".
 *
 * @property integer    $user_id
 * @property integer    $tournament_id
 * @property User       $user
 * @property Tournament $tournament
 */
class LanguageOfficer extends \yii\db\ActiveRecord {
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'language_officer';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['user_id', 'tournament_id'], 'required'],
			[['user_id', 'tournament_id'], 'integer']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'user_id' => Yii::t('app', 'User ID'),
			'tournament_id' => Yii::t('app', 'Tournament ID'),
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
	public function getTournament() {
		return $this->hasOne(Tournament::className(), ['id' => 'tournament_id']);
	}
}
