<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "team".
 *
 * @property integer $id
 * @property string $name
 * @property integer $tournament_id
 * @property integer $speakerA_id
 * @property integer $speakerB_id
 *
 * @property DrawPosition[] $drawPositions
 * @property InSociety $inSocieties
 * @property Strikes[] $strikes
 * @property Adjudicator[] $adjudicators
 * @property Tournament $tournament
 * @property User $speakerA
 * @property User $speakerB
 */
class Team extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'team';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['tournament_id', 'name', 'speakerA_id', 'speakerB_id'], 'required'],
            [['tournament_id', 'speakerA_id', 'speakerB_id'], 'integer'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Team Name'),
            'tournament_id' => Yii::t('app', 'Tournament ID'),
            'speakerA_id' => Yii::t('app', 'Speaker A'),
            'speakerB_id' => Yii::t('app', 'Speaker B'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDrawPositions() {
        return $this->hasMany(DrawPosition::className(), ['team_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStrikes() {
        return $this->hasMany(Strikes::className(), ['team_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStrikedAdjudicators() {
        return $this->hasMany(Adjudicator::className(), ['id' => 'adjudicator_id'])->viaTable('strikes', ['team_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTournament() {
        return $this->hasOne(Tournament::className(), ['id' => 'tournament_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpeakerA() {
        return $this->hasOne(User::className(), ['id' => 'speakerA_id'])->from('user uA');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpeakerB() {
        return $this->hasOne(User::className(), ['id' => 'speakerB_id'])->from('user uB');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInSocieties() {
        return InSociety::find()->where("user_id IN (:userA, :userB)", [
                    ":userA" => $this->speakerA_id,
                    ":userB" => $this->speakerB_id,
        ]);
    }

}
