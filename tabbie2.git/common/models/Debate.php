<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "debate".
 *
 * @property integer $id
 * @property integer $round_id
 * @property integer $tournament_id
 * @property integer $og_team_id
 * @property integer $oo_team_id
 * @property integer $cg_team_id
 * @property integer $co_team_id
 * @property integer $panel_id
 * @property integer $venue_id
 * @property integer $og_feedback
 * @property integer $oo_feedback
 * @property integer $cg_feedback
 * @property integer $co_feedback
 * @property string $time
 *
 * @property Panel $panel
 * @property Venue $venue
 * @property Feedback[] $feedbacks
 * @property Result[] $results
 */
class Debate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'debate';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['round_id', 'tournament_id', 'og_team_id', 'oo_team_id', 'cg_team_id', 'co_team_id', 'panel_id', 'venue_id'], 'required'],
            [['round_id', 'tournament_id', 'og_team_id', 'oo_team_id', 'cg_team_id', 'co_team_id', 'panel_id', 'venue_id', 'og_feedback', 'oo_feedback', 'cg_feedback', 'co_feedback'], 'integer'],
            [['time'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'round_id' => Yii::t('app', 'Round ID'),
            'tournament_id' => Yii::t('app', 'Tournament ID'),
            'og_team_id' => Yii::t('app', 'Og Team ID'),
            'oo_team_id' => Yii::t('app', 'Oo Team ID'),
            'cg_team_id' => Yii::t('app', 'Cg Team ID'),
            'co_team_id' => Yii::t('app', 'Co Team ID'),
            'panel_id' => Yii::t('app', 'Panel ID'),
            'venue_id' => Yii::t('app', 'Venue ID'),
            'og_feedback' => Yii::t('app', 'Og Feedback'),
            'oo_feedback' => Yii::t('app', 'Oo Feedback'),
            'cg_feedback' => Yii::t('app', 'Cg Feedback'),
            'co_feedback' => Yii::t('app', 'Co Feedback'),
            'time' => Yii::t('app', 'Time'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPanel()
    {
        return $this->hasOne(Panel::className(), ['id' => 'panel_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVenue()
    {
        return $this->hasOne(Venue::className(), ['id' => 'venue_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFeedbacks()
    {
        return $this->hasMany(Feedback::className(), ['debate_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getResults()
    {
        return $this->hasMany(Result::className(), ['debate_id' => 'id']);
    }
}
