<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "adjudicator".
 *
 * @property integer $id
 * @property integer $tournament_id
 * @property integer $user_id
 * @property integer $strength
 *
 * @property Tournament $tournament
 * @property User $user
 * @property AdjudicatorInPanel[] $adjudicatorInPanels
 * @property Panel[] $panels
 * @property Strikes[] $strikes
 * @property Team[] $teams
 */
class Adjudicator extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'adjudicator';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tournament_id', 'user_id'], 'required'],
            [['tournament_id', 'user_id', 'strength'], 'integer']
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
            'user_id' => Yii::t('app', 'User ID'),
            'strength' => Yii::t('app', 'Strength'),
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
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdjudicatorInPanels()
    {
        return $this->hasMany(AdjudicatorInPanel::className(), ['adjudicator_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPanels()
    {
        return $this->hasMany(Panel::className(), ['id' => 'panel_id'])->viaTable('adjudicator_in_panel', ['adjudicator_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStrikes()
    {
        return $this->hasMany(Strikes::className(), ['adjudicator_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeams()
    {
        return $this->hasMany(Team::className(), ['id' => 'team_id'])->viaTable('strikes', ['adjudicator_id' => 'id']);
    }
}
