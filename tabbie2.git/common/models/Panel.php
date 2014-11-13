<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "panel".
 *
 * @property integer $id
 * @property integer $strength
 * @property string $time
 * @property integer $tournament_id
 * @property integer $used
 *
 * @property AdjudicatorInPanel[] $adjudicatorInPanels
 * @property Adjudicator[] $adjudicators
 * @property Debate[] $debates
 * @property Tournament $tournament
 */
class Panel extends \yii\db\ActiveRecord {

    const FUNCTION_CHAIR = 1;
    const FUNCTION_WING = 0;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'panel';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['strength', 'tournament_id', 'used'], 'integer'],
            [['time'], 'safe'],
            [['tournament_id'], 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'strength' => Yii::t('app', 'Strength'),
            'time' => Yii::t('app', 'Time'),
            'tournament_id' => Yii::t('app', 'Tournament ID'),
            'used' => Yii::t('app', 'Used'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdjudicatorInPanels() {
        return $this->hasMany(AdjudicatorInPanel::className(), ['panel_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdjudicators() {
        return $this->hasMany(Adjudicator::className(), ['id' => 'adjudicator_id'])->viaTable('adjudicator_in_panel', ['panel_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDebates() {
        return $this->hasMany(Debate::className(), ['panel_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTournament() {
        return $this->hasOne(Tournament::className(), ['id' => 'tournament_id']);
    }

}
