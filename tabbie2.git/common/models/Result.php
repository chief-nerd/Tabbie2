<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "result".
 *
 * @property integer $id
 * @property integer $debate_id
 * @property integer $og_speaks
 * @property integer $og_place
 * @property integer $oo_speaks
 * @property integer $oo_place
 * @property integer $cg_speaks
 * @property integer $cg_place
 * @property integer $co_speaks
 * @property integer $co_place
 * @property string $time
 *
 * @property DrawPosition[] $drawPositions
 * @property Debate $debate
 */
class Result extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'result';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['debate_id', 'og_speaks', 'oo_speaks', 'cg_speaks', 'co_speaks'], 'required'],
            [['debate_id', 'og_speaks', 'og_place', 'oo_speaks', 'oo_place', 'cg_speaks', 'cg_place', 'co_speaks', 'co_place'], 'integer'],
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
            'debate_id' => Yii::t('app', 'Debate ID'),
            'og_speaks' => Yii::t('app', 'Og Speaks'),
            'og_place' => Yii::t('app', 'Og Place'),
            'oo_speaks' => Yii::t('app', 'Oo Speaks'),
            'oo_place' => Yii::t('app', 'Oo Place'),
            'cg_speaks' => Yii::t('app', 'Cg Speaks'),
            'cg_place' => Yii::t('app', 'Cg Place'),
            'co_speaks' => Yii::t('app', 'Co Speaks'),
            'co_place' => Yii::t('app', 'Co Place'),
            'time' => Yii::t('app', 'Time'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDrawPositions()
    {
        return $this->hasMany(DrawPosition::className(), ['result_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDebate()
    {
        return $this->hasOne(Debate::className(), ['id' => 'debate_id']);
    }
}
