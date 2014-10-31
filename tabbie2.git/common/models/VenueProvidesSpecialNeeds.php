<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "venue_provides_special_needs".
 *
 * @property integer $venue_id
 * @property integer $special_needs_id
 *
 * @property SpecialNeeds $specialNeeds
 * @property Venue $venue
 */
class VenueProvidesSpecialNeeds extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'venue_provides_special_needs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['venue_id', 'special_needs_id'], 'required'],
            [['venue_id', 'special_needs_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'venue_id' => Yii::t('app', 'Venue ID'),
            'special_needs_id' => Yii::t('app', 'Special Needs ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpecialNeeds()
    {
        return $this->hasOne(SpecialNeeds::className(), ['id' => 'special_needs_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVenue()
    {
        return $this->hasOne(Venue::className(), ['id' => 'venue_id']);
    }
}
