<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "country".
 *
 * @property integer $id
 * @property string $name
 * @property string $alpha_2
 * @property string $alpha_3
 * @property integer $region_id
 *
 * @property Society[] $societies
 */
class Country extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'country';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['region_id'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['alpha_2'], 'string', 'max' => 2],
            [['alpha_3'], 'string', 'max' => 3]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'alpha_2' => Yii::t('app', 'Alpha 2'),
            'alpha_3' => Yii::t('app', 'Alpha 3'),
            'region_id' => Yii::t('app', 'Region ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSocieties()
    {
        return $this->hasMany(Society::className(), ['country_id' => 'id']);
    }
}
