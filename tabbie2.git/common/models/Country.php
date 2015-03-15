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

	const REGION_NORTHERN_EUROPE = 11;
	const REGION_WESTERN_EUROPE  = 12;
	const REGION_SOUTHERN_EUROPE = 13;
	const REGION_EASTERN_EUROPE  = 14;

	const REGION_CENTRAL_ASIA       = 21;
	const REGION_EASTERN_ASIA       = 22;
	const REGION_WESTERN_ASIA       = 23;
	const REGION_SOUTHERN_ASIA      = 24;
	const REGION_SOUTH_EASTERN_ASIA = 25;

	const REGION_AUSTRALIA_NEW_ZEALAND = 31;
	const REGION_MICRONESIA            = 32;
	const REGION_MELANESIA             = 33;
	const REGION_POLYNESIA             = 34;

	const REGION_NORTHERN_AFRICA = 41;
	const REGION_WESTERN_AFRICA  = 42;
	const REGION_CENTRAL_AFRICA  = 43;
	const REGION_EASTERN_AFTRICA = 44;
	const REGION_SOUTHERN_AFRICA = 45;

	const REGION_NORTHERN_AMERICA = 51;
	const REGION_CENTRAL_AMERICA  = 52;
	const REGION_CARIBBEAN        = 53;

	const REGION_SOUTH_AMERICA = 61;

	const REGION_ANTARCTIC = 71;

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


	public static function getRegionLabel($region_id = null) {
		$regions = [
			0 => Yii::t("country", "Undefined"),
			self::REGION_NORTHERN_EUROPE => Yii::t("country", "Northern Europe"),
			self::REGION_WESTERN_EUROPE => Yii::t("country", "Western Europe"),
			self::REGION_SOUTHERN_EUROPE => Yii::t("country", "Southern Europe"),
			self::REGION_EASTERN_EUROPE => Yii::t("country", "Eastern Europe"),
			self::REGION_CENTRAL_ASIA => Yii::t("country", "Central Asia"),
			self::REGION_EASTERN_ASIA => Yii::t("country", "Eastern Asia"),
			self::REGION_WESTERN_ASIA => Yii::t("country", "Western Asia"),
			self::REGION_SOUTHERN_ASIA => Yii::t("country", "Southern Asia"),
			self::REGION_SOUTH_EASTERN_ASIA => Yii::t("country", "South-Eastern Asia"),
			self::REGION_AUSTRALIA_NEW_ZEALAND => Yii::t("country", "Australia & New Zealand"),
			self::REGION_MICRONESIA => Yii::t("country", "Micronesia"),
			self::REGION_MELANESIA => Yii::t("country", "Melanesia"),
			self::REGION_POLYNESIA => Yii::t("country", "Polynesia"),
			self::REGION_NORTHERN_AFRICA => Yii::t("country", "Northern Africa"),
			self::REGION_WESTERN_AFRICA => Yii::t("country", "Western Africa"),
			self::REGION_CENTRAL_AFRICA => Yii::t("country", "Central Africa"),
			self::REGION_EASTERN_AFTRICA => Yii::t("country", "Eastern Africa"),
			self::REGION_SOUTHERN_AFRICA => Yii::t("country", "Southern Africa"),
			self::REGION_NORTHERN_AMERICA => Yii::t("country", "Northern Africa"),
			self::REGION_CENTRAL_AMERICA => Yii::t("country", "Central Africa"),
			self::REGION_CARIBBEAN => Yii::t("country", "Caribbean"),
			self::REGION_SOUTH_AMERICA => Yii::t("country", "South America"),
			self::REGION_SOUTH_AMERICA => Yii::t("country", "Antarctic"),
		];

		return (isset($regions[$region_id])) ? $regions[$region_id] : $regions;
	}
}
