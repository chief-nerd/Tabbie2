<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "publish_tab_team".
 *
 * @property integer $id
 * @property integer $tournament_id
 * @property integer $team_id
 * @property integer $enl_place
 * @property integer $esl_place
 * @property string $cache_results
 * @property integer $speaks
 *
 * @property Team $team
 * @property Tournament $tournament
 */
class PublishTabTeam extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'publish_tab_team';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'tournament_id', 'team_id'], 'required'],
            [['id', 'tournament_id', 'team_id', 'enl_place', 'esl_place', 'speaks'], 'integer'],
            [['cache_results'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'tournament_id' => Yii::t('app', 'Tournament ID'),
            'team_id' => Yii::t('app', 'Team ID'),
            'enl_place' => Yii::t('app', 'ENL Place'),
            'esl_place' => Yii::t('app', 'ESL Place'),
            'cache_results' => Yii::t('app', 'Cache Results'),
            'speaks' => Yii::t('app', 'Speaker Points'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeam() {
        return $this->hasOne(Team::className(), ['id' => 'team_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTournament() {
        return $this->hasOne(Tournament::className(), ['id' => 'tournament_id']);
    }

}
