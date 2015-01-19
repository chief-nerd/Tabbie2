<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "energy_config".
 *
 * @property integer $id
 * @property string $key
 * @property integer $tournament_id
 * @property string $label
 * @property integer $value
 *
 * @property Tournament $tournament
 */
class EnergyConfig extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'energy_config';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['key', 'tournament_id', 'label'], 'required'],
            [['tournament_id', 'value'], 'integer'],
            [['key'], 'string', 'max' => 100],
            [['label'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'key' => Yii::t('app', 'Key'),
            'tournament_id' => Yii::t('app', 'Tournament ID'),
            'label' => Yii::t('app', 'Label'),
            'value' => Yii::t('app', 'Value'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTournament() {
        return $this->hasOne(Tournament::className(), ['id' => 'tournament_id']);
    }

    public function setup($tournament) {
        $algo = $tournament->getTabAlgorithmInstance();
        if ($algo->setup($this))
            return true;
        else
            return false;
    }

}
