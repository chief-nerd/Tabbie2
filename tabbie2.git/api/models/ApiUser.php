<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "api_user".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $access_token
 * @property string $rl_timestamp
 * @property integer $rl_remaining
 *
 * @property User $user
 */
class ApiUser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'rl_remaining'], 'integer'],
            [['rl_timestamp'], 'safe'],
            [['access_token'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'access_token' => Yii::t('app', 'Access Token'),
            'rl_timestamp' => Yii::t('app', 'Rl Timestamp'),
            'rl_remaining' => Yii::t('app', 'Rl Remaining'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
