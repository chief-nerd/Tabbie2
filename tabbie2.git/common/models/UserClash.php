<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_clash".
 *
 * @property integer             $id
 * @property integer             $user_id
 * @property integer             $clash_with
 * @property string              $reason
 * @property string              $date
 *
 * @property AdjudicatorStrike[] $adjudicatorStrikes
 * @property TeamStrike[]        $teamStrikes
 * @property User                $user
 * @property User                $clashWith
 */
class UserClash extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'user_clash';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['user_id', 'clash_with'], 'required'],
			[['user_id', 'clash_with'], 'integer'],
			[['date'], 'safe'],
			[['reason'], 'string', 'max' => 255],
			//[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['id']],
			//[['clash_with'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['id']],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'         => Yii::t('app', 'ID'),
			'user_id'    => Yii::t('app', 'User ID'),
			'clash_with' => Yii::t('app', 'Clash With'),
			'reason'     => Yii::t('app', 'Reason'),
			'date'       => Yii::t('app', 'Date'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getAdjudicatorStrikes()
	{
		return $this->hasMany(AdjudicatorStrike::className(), ['user_clash_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTeamStrikes()
	{
		return $this->hasMany(TeamStrike::className(), ['user_clash_id' => 'id']);
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
	public function getClashWith()
	{
		return $this->hasOne(User::className(), ['id' => 'clash_with']);
	}
}
