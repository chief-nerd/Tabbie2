<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "country".
 *
 * @property integer   $id
 * @property string    $name
 * @property string    $alpha_2
 * @property string    $alpha_3
 * @property integer   $region_id
 * @property Society[] $societies
 */
class Country extends \yii\db\ActiveRecord
{

	//Key of an Unknown Country
	const COUNTRY_UNKNOWN_ID = 250;

	//Blue
	const REGION_NORTHERN_EUROPE = 11; //#28334C
	const REGION_WESTERN_EUROPE = 12; //#4B6FC8
	const REGION_SOUTHERN_EUROPE = 13; //#47689B
	const REGION_EASTERN_EUROPE = 14; //#616B7F

	//Red
	const REGION_CENTRAL_ASIA = 21; //#DE9079
	const REGION_EASTERN_ASIA = 22; //#DA4A2F
	const REGION_WESTERN_ASIA = 23; //#C0334F
	const REGION_SOUTHERN_ASIA = 24; //#AB5C4F
	const REGION_SOUTH_EASTERN_ASIA = 25; //#EC4F66

	//Purpel
	const REGION_AUSTRALIA_NEW_ZEALAND = 31; //#CE50E1
	const REGION_MICRONESIA = 32; //#8B5EA4
	const REGION_MELANESIA = 33; //#8E64DA
	const REGION_POLYNESIA = 34; //#C884DC

	//Green
	const REGION_NORTHERN_AFRICA = 41; //#638853
	const REGION_WESTERN_AFRICA = 42; //#75DE3B
	const REGION_CENTRAL_AFRICA = 43; //#99D995
	const REGION_EASTERN_AFTRICA = 44; //#5C9D34
	const REGION_SOUTHERN_AFRICA = 45; //#88E070

	//Yellow
	const REGION_NORTHERN_AMERICA = 51; //#D6CA73
	const REGION_CENTRAL_AMERICA = 52; //#D3D239
	const REGION_CARIBBEAN = 53; //#878033

	//Turkis
	const REGION_SOUTH_AMERICA = 61; //#42DDBB

	//White
	const REGION_ANTARCTIC = 71; //#FFFFFF

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
	public function rules() {
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
	public function attributeLabels() {
		return [
			'id'        => Yii::t('app', 'ID'),
			'name'      => Yii::t('app', 'Name'),
			'alpha_2'   => Yii::t('app', 'Alpha 2'),
			'alpha_3'   => Yii::t('app', 'Alpha 3'),
			'region_id' => Yii::t('app', 'Region') . ' ' . Yii::t('app', 'ID'),
		];
	}

	public static function getRegionLabel($region_id = null)
	{
		$regions = [
			0                                  => Yii::t("app.country", "Undefined"),
			self::REGION_NORTHERN_EUROPE       => Yii::t("app.country", "Northern Europe"),
			self::REGION_WESTERN_EUROPE        => Yii::t("app.country", "Western Europe"),
			self::REGION_SOUTHERN_EUROPE       => Yii::t("app.country", "Southern Europe"),
			self::REGION_EASTERN_EUROPE        => Yii::t("app.country", "Eastern Europe"),
			self::REGION_CENTRAL_ASIA          => Yii::t("app.country", "Central Asia"),
			self::REGION_EASTERN_ASIA          => Yii::t("app.country", "Eastern Asia"),
			self::REGION_WESTERN_ASIA          => Yii::t("app.country", "Western Asia"),
			self::REGION_SOUTHERN_ASIA         => Yii::t("app.country", "Southern Asia"),
			self::REGION_SOUTH_EASTERN_ASIA    => Yii::t("app.country", "South-Eastern Asia"),
			self::REGION_AUSTRALIA_NEW_ZEALAND => Yii::t("app.country", "Australia & New Zealand"),
			self::REGION_MICRONESIA            => Yii::t("app.country", "Micronesia"),
			self::REGION_MELANESIA             => Yii::t("app.country", "Melanesia"),
			self::REGION_POLYNESIA             => Yii::t("app.country", "Polynesia"),
			self::REGION_NORTHERN_AFRICA       => Yii::t("app.country", "Northern Africa"),
			self::REGION_WESTERN_AFRICA        => Yii::t("app.country", "Western Africa"),
			self::REGION_CENTRAL_AFRICA        => Yii::t("app.country", "Central Africa"),
			self::REGION_EASTERN_AFTRICA       => Yii::t("app.country", "Eastern Africa"),
			self::REGION_SOUTHERN_AFRICA       => Yii::t("app.country", "Southern Africa"),
			self::REGION_NORTHERN_AMERICA      => Yii::t("app.country", "Northern America"),
			self::REGION_CENTRAL_AMERICA       => Yii::t("app.country", "Central America"),
			self::REGION_CARIBBEAN             => Yii::t("app.country", "Caribbean"),
			self::REGION_SOUTH_AMERICA         => Yii::t("app.country", "South America"),
			self::REGION_ANTARCTIC             => Yii::t("app.country", "Antarctic"),
		];

		return (isset($regions[$region_id])) ? $regions[$region_id] : $regions;
	}

	public static function getCSSLabel($region_id = null)
	{
		$regions = [
			0                                  => "",
			self::REGION_NORTHERN_EUROPE       => "NOEU",
			self::REGION_WESTERN_EUROPE        => "WEEU",
			self::REGION_SOUTHERN_EUROPE       => "SOEU",
			self::REGION_EASTERN_EUROPE        => "EAEU",
			self::REGION_CENTRAL_ASIA          => "CEAS",
			self::REGION_EASTERN_ASIA          => "EAAS",
			self::REGION_WESTERN_ASIA          => "WEAS",
			self::REGION_SOUTHERN_ASIA         => "SOAS",
			self::REGION_SOUTH_EASTERN_ASIA    => "EAAS",
			self::REGION_AUSTRALIA_NEW_ZEALAND => "AUZE",
			self::REGION_MICRONESIA            => "MICR",
			self::REGION_MELANESIA             => "MELA",
			self::REGION_POLYNESIA             => "POLY",
			self::REGION_NORTHERN_AFRICA       => "NOAF",
			self::REGION_WESTERN_AFRICA        => "WEAF",
			self::REGION_CENTRAL_AFRICA        => "CEAF",
			self::REGION_EASTERN_AFTRICA       => "EAAF",
			self::REGION_SOUTHERN_AFRICA       => "SOAF",
			self::REGION_NORTHERN_AMERICA      => "NOAM",
			self::REGION_CENTRAL_AMERICA       => "CEAM",
			self::REGION_CARIBBEAN             => "CARI",
			self::REGION_SOUTH_AMERICA         => "SOAM",
			self::REGION_ANTARCTIC             => "ANTA",
		];

		return (isset($regions[$region_id])) ? $regions[$region_id] : $regions;
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getSocieties()
	{
		return $this->hasMany(Society::className(), ['country_id' => 'id']);
	}

	public function getRegionName()
	{
		return self::getRegionLabel($this->region_id);
	}
}
