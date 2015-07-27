<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "convenor".
 *
 * @property integer    $tournament_id
 * @property integer    $user_id
 *
 * @property Tournament $tournament
 * @property User       $user
 */
class Convenor extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'convenor';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['tournament_id', 'user_id'], 'required'],
			[['tournament_id', 'user_id'], 'integer'],
			//[['tournament_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tournament::className(), 'targetAttribute' => ['id']],
			//[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['id']],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'tournament_id' => Yii::t('app', 'Tournament ID'),
			'user_id'       => Yii::t('app', 'User ID'),
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
	public function getTournament()
	{
		return $this->hasOne(Tournament::className(), ['id' => 'tournament_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser()
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}
}
