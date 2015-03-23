<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "publish_tab_speaker".
 *
 * @property integer    $id
 * @property integer    $tournament_id
 * @property integer    $user_id
 * @property string     $enl_place
 * @property string     $esl_place
 * @property string     $cache_results
 * @property User       $user
 * @property Tournament $tournament
 */
class PublishTabSpeaker extends \yii\db\ActiveRecord {

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'publish_tab_speaker';
	}

	/**
	 * @inheritdoc
	 * @return TournamentQuery
	 */
	public static function find() {
		return new TournamentQuery(get_called_class());
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['id', 'tournament_id', 'user_id'], 'required'],
			[['id', 'tournament_id', 'user_id', 'esl_place', 'enl_place'], 'integer'],
			[['cache_results'], 'string'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('app', 'ID'),
			'tournament_id' => Yii::t('app', 'Tournament ID'),
			'user_id' => Yii::t('app', 'User ID'),
			'enl_place' => Yii::t('app', 'ENL Place'),
			'esl_place' => Yii::t('app', 'ESL Place'),
			'cache_results' => Yii::t('app', 'Cache Result'),
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
