<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_attr".
 *
 * @property integer $id
 * @property integer $tournament_id
 * @property string $name
 * @property integer $required
 * @property string $help
 *
 * @property Tournament $tournament
 * @property UserValue[] $userValues
 */
class UserAttr extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_attr';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tournament_id', 'name'], 'required'],
            [['tournament_id', 'required'], 'integer'],
            [['help'], 'string'],
            [['name'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'tournament_id' => Yii::t('app', 'Tournament ID'),
            'name' => Yii::t('app', 'Name'),
            'required' => Yii::t('app', 'Required'),
            'help' => Yii::t('app', 'Help'),
        ];
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
    public function getUserValues()
    {
        return $this->hasMany(UserValue::className(), ['user_attr_id' => 'id']);
    }
}
