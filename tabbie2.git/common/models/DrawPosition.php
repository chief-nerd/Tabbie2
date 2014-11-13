<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "draw_position".
 *
 * @property integer $id
 * @property integer $draw_id
 * @property integer $team_id
 * @property integer $result_id
 * @property integer $points
 * @property integer $speakerA_speaks
 * @property integer $speakerB_speaks
 *
 * @property DrawAfterRound $draw
 * @property Result $result
 * @property Team $team
 */
class DrawPosition extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'draw_position';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['draw_id', 'team_id', 'result_id', 'points', 'speakerA_speaks', 'speakerB_speaks'], 'required'],
            [['draw_id', 'team_id', 'result_id', 'points', 'speakerA_speaks', 'speakerB_speaks'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'draw_id' => Yii::t('app', 'Draw ID'),
            'team_id' => Yii::t('app', 'Team ID'),
            'result_id' => Yii::t('app', 'Result ID'),
            'points' => Yii::t('app', 'Points'),
            'speakerA_speaks' => Yii::t('app', 'Speaker A Speaks'),
            'speakerB_speaks' => Yii::t('app', 'Speaker B Speaks'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDraw()
    {
        return $this->hasOne(DrawAfterRound::className(), ['id' => 'draw_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getResult()
    {
        return $this->hasOne(Result::className(), ['id' => 'result_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeam()
    {
        return $this->hasOne(Team::className(), ['id' => 'team_id']);
    }
}
