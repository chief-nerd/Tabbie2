<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 * This is the model class for table "user". It represents a single user in the system.
 * @see Team
 * @see Adjudicator
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property integer $role
 * @property integer $status
 * @property integer $last_change
 * @property string $givenname
 * @property string $surename
 * @property string $picture
 * @property string $time
 *
 * @property Adjudicator[] $adjudicators
 * @property InSociety[] $inSocieties
 * @property Society[] $societies
 * @property Team[] $teams
 * @property SpecialNeeds[] $specialNeeds
 */
class User extends ActiveRecord implements IdentityInterface {

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;
    const ROLE_USER = 10;
    const ROLE_TABMASTER = 11;
    const ROLE_ADMIN = 12;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'time',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'last_change',
                ],
                'value' => function() {
            return date('Y-m-d H:i:s');
        }, // unix timestamp
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            ['username', 'validateIsUrlAllowed'],
            ['role', 'default', 'value' => self::ROLE_USER],
            ['role', 'in', 'range' => [self::ROLE_USER, self::ROLE_TABMASTER, self::ROLE_ADMIN]],
            [['username', 'auth_key', 'password_hash', 'email'], 'required'],
            [['role', 'status'], 'integer'],
            [['picture'], 'string'],
            [['auth_key', 'time', 'last_change'], 'safe'],
            [['username', 'password_hash', 'password_reset_token', 'email', 'givenname', 'surename'], 'string', 'max' => 255],
        ];
    }

    public function validateIsUrlAllowed($attribute, $params) {
        $actions = ["create", "update", "view", "delete", "list"];
        if (in_array($this->$attribute, $actions)) {
            $this->addError($attribute, 'This Username is not allowed');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', 'Username'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'password_hash' => Yii::t('app', 'Password Hash'),
            'password_reset_token' => Yii::t('app', 'Password Reset Token'),
            'email' => Yii::t('app', 'Email'),
            'role' => Yii::t('app', 'Account Role'),
            'status' => Yii::t('app', 'Account Status'),
            'last_change' => Yii::t('app', 'Last Change'),
            'givenname' => Yii::t('app', 'Givenname'),
            'surename' => Yii::t('app', 'Surename'),
            'picture' => Yii::t('app', 'Picture'),
            'time' => Yii::t('app', 'Time'),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id) {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username) {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token) {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
                    'password_reset_token' => $token,
                    'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token) {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey() {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey) {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password) {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password) {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey() {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken() {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken() {
        $this->password_reset_token = null;
    }

    /**
     * Returns the full name of the User
     * @return string
     */
    public function getName() {
        return $this->givenname . " " . $this->surename;
    }

    /**
     * Returns all Adjudicator for this user
     * @return \yii\db\ActiveQuery
     */
    public function getAdjudicators() {
        return $this->hasMany(Adjudicator::className(), ['user_id' => 'id']);
    }

    /**
     * Returns all Teams for this user
     * @return type
     */
    public function getTeams() {
        return $this->hasMany(Team::className(), ['speakerB_id' => 'id']);
    }

    /**
     * Returns all InSociety Connections for this user
     * @return \yii\db\ActiveQuery
     */
    public function getInSocieties() {
        return $this->hasMany(InSociety::className(), ['username_id' => 'id']);
    }

    /**
     * Returns all Societies for this user
     * @return \yii\db\ActiveQuery
     */
    public function getSocieties() {
        return $this->hasMany(Society::className(), ['id' => 'society_id'])->viaTable('in_society', ['username_id' => 'id']);
    }

    /**
     * Returns all the Special Needs for a User
     * @return \yii\db\ActiveQuery
     */
    public function getSpecialNeeds() {
        return $this->hasMany(SpecialNeeds::className(), ['id' => 'special_needs_id'])->viaTable('username_has_special_needs', ['username_id' => 'id']);
    }

    public static function getRoleOptions($none = false) {
        $options = [
            self::ROLE_USER => self::getRoleLabel(User::ROLE_USER),
            self::ROLE_TABMASTER => self::getRoleLabel(User::ROLE_TABMASTER),
            self::ROLE_ADMIN => self::getRoleLabel(User::ROLE_ADMIN),
        ];
        if ($none) {
            $options = array_merge(["" => ''], $options);
        }
        return $options;
    }

    public static function getRoleLabel($id) {
        switch ($id) {
            case self::ROLE_USER:
                return Yii::t("app", "User");
            case self::ROLE_TABMASTER:
                return Yii::t("app", "Tabmaster");
            case self::ROLE_ADMIN:
                return Yii::t("app", "Admin");
        }
    }

    public static function getStatusOptions() {
        return [
            self::STATUS_ACTIVE => self::getStatusLabel(User::STATUS_ACTIVE),
            self::STATUS_DELETED => self::getStatusLabel(User::STATUS_DELETED),
        ];
    }

    public static function getStatusLabel($id) {
        switch ($id) {
            case self::STATUS_ACTIVE:
                return Yii::t("app", "Active");
            case self::STATUS_DELETED:
                return Yii::t("app", "Deleted");
        }
    }

}
