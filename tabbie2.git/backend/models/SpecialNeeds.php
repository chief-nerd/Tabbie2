<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "special_needs".
 *
 * @property integer $id
 * @property string $name
 *
 * @property UsernameHasSpecialNeeds[] $usernameHasSpecialNeeds
 * @property User[] $usernames
 * @property VenueProvidesSpecialNeeds[] $venueProvidesSpecialNeeds
 * @property Venue[] $venues
 */
class SpecialNeeds extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'special_needs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsernameHasSpecialNeeds()
    {
        return $this->hasMany(UsernameHasSpecialNeeds::className(), ['special_needs_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsernames()
    {
        return $this->hasMany(User::className(), ['id' => 'username_id'])->viaTable('username_has_special_needs', ['special_needs_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVenueProvidesSpecialNeeds()
    {
        return $this->hasMany(VenueProvidesSpecialNeeds::className(), ['special_needs_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVenues()
    {
        return $this->hasMany(Venue::className(), ['id' => 'venue_id'])->viaTable('venue_provides_special_needs', ['special_needs_id' => 'id']);
    }
}
