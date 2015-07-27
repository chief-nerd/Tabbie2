<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tabmaster".
 *
 * @property integer    $tabmaster_id
 * @property integer    $tournament_id
 *
 * @property User       $tabmaster
 * @property Tournament $tournament
 */
class Tabmaster extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'tabmaster';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['user_id', 'tournament_id'], 'required'],
			[['user_id', 'tournament_id'], 'integer'],
			//[['tabmaster_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['id']],
			//[['tournament_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tournament::className(), 'targetAttribute' => ['id']],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'user_id'       => Yii::t('app', 'Tabmaster User ID'),
			'tournament_id' => Yii::t('app', 'Tournament ID'),
		];
	}

	/**
	 * @inheritdoc
	 * @return TournamentQuery
	 */
	public static function find()
	{
		return new TournamentQuery(get_called_class());
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser()
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTournament()
	{
		return $this->hasOne(Tournament::className(), ['id' => 'tournament_id']);
	}
}
