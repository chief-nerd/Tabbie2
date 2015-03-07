<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "society".
 *
 * @property integer $id
 * @property string $fullname
 * @property string $abr
 * @property string $city
 * @property string $country
 *
 * @property Adjudicator[] $adjudicators
 * @property InSociety[] $inSocieties
 * @property User[] $usernames
 * @property Team[] $teams
 */
class Society extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'society';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['fullname', 'city', 'country'], 'string', 'max' => 255],
            [['abr'], 'string', 'max' => 45],
            [['abr'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'fullname' => Yii::t('app', 'Fullname'),
            'abr' => Yii::t('app', 'Abbrevation'),
            'city' => Yii::t('app', 'City'),
            'country' => Yii::t('app', 'Country'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdjudicators() {
        return $this->hasMany(Adjudicator::className(), ['society_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInSocieties() {
        return $this->hasMany(InSociety::className(), ['society_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsernames() {
        return $this->hasMany(User::className(), ['id' => 'username_id'])->viaTable('in_society', ['society_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeams() {
        return $this->hasMany(Team::className(), ['society_id' => 'id']);
    }

    public static function generateAbr($name) {
        $abr = 0;
        $parts = explode(" ", trim($name));
        foreach ($parts as $part) {
            $abr .= $part[0];
        }
        return strtoupper($abr);
    }

}
