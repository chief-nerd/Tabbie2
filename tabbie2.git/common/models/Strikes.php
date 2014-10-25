<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "strikes".
 *
 * @property integer $team_id
 * @property integer $adjudicator_id
 * @property integer $approved
 *
 * @property Adjudicator $adjudicator
 * @property Team $team
 */
class Strikes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'strikes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['team_id', 'adjudicator_id'], 'required'],
            [['team_id', 'adjudicator_id', 'approved'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'team_id' => Yii::t('app', 'Team ID'),
            'adjudicator_id' => Yii::t('app', 'Adjudicator ID'),
            'approved' => Yii::t('app', 'Approved'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdjudicator()
    {
        return $this->hasOne(Adjudicator::className(), ['id' => 'adjudicator_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeam()
    {
        return $this->hasOne(Team::className(), ['id' => 'team_id']);
    }
}
