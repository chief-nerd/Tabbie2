<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "draw_after_round".
 *
 * @property integer $id
 * @property integer $tournament_id
 * @property integer $round_id
 * @property string $time
 *
 * @property Round $round
 * @property Tournament $tournament
 * @property DrawPosition[] $drawPositions
 */
class DrawAfterRound extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'draw_after_round';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tournament_id', 'round_id'], 'required'],
            [['tournament_id', 'round_id'], 'integer'],
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
            'tournament_id' => Yii::t('app', 'Tournament ID'),
            'round_id' => Yii::t('app', 'Round ID'),
            'time' => Yii::t('app', 'Time'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRound()
    {
        return $this->hasOne(Round::className(), ['id' => 'round_id']);
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
    public function getDrawPositions()
    {
        return $this->hasMany(DrawPosition::className(), ['draw_id' => 'id']);
    }
}
