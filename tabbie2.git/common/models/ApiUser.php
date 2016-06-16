<?php

namespace common\models;

use Yii;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\RateLimitInterface;
use yii\web\IdentityInterface;

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
class ApiUser extends \yii\db\ActiveRecord implements IdentityInterface, RateLimitInterface
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

    /**
     * Find user by AccessToken for API request
     *
     * @param mixed $token
     * @param null $type
     *
     * @return null|static
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        switch($type)
        {
            case HttpBasicAuth::className():
                return static::find()->where(['access_token' => $token])->one();
        }

        return false;
    }

    public function getRateLimit($request, $action)
    {
        return [$this->rateLimit, Yii::$app->params['requestsPerSecond']]; // $rateLimit requests per second
    }

    public function loadAllowance($request, $action)
    {
        return [$this->rl_remaining, $this->rl_timestamp];
    }

    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        $this->rl_remaining = $allowance;
        $this->rl_timestamp = $timestamp;
        $this->save();
    }

    /**
     * Finds an identity by the given ID.
     * @param string|integer $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return User::findIdentity($id);
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|integer an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->user->getId();
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->user->getAuthKey();
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return boolean whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $this->user->validateAuthKey($authKey);
    }
}
