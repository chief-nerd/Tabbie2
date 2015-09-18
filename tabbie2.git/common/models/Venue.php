<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "venue".
 *
 * @property integer                     $id
 * @property integer                     $tournament_id
 * @property string                      $name
 * @property boolean                     $active
 * @property string                      $group
 * @property Debate[]                    $debates
 * @property Tournament                  $tournament
 * @property VenueProvidesSpecialNeeds[] $venueProvidesSpecialNeeds
 * @property SpecialNeeds[]              $specialNeeds
 */
class Venue extends \yii\db\ActiveRecord
{

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'venue';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['tournament_id', 'name'], 'required'],
			[['tournament_id', 'active'], 'integer'],
			[['name', 'group'], 'string', 'max' => 100]
		];
	}

	/**
	 * @inheritdoc
	 * @return VTAQuery
	 */
	public static function find()
	{
		return new VTAQuery(get_called_class());
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'            => Yii::t('app', 'ID'),
			'tournament_id' => Yii::t('app', 'Tournament') . ' ' . Yii::t('app', 'ID'),
			'name'          => Yii::t('app', 'Name'),
			'group'         => Yii::t('app', 'Group'),
			'active'        => Yii::t('app', 'Active Room'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getDebates()
	{
		return $this->hasMany(Debate::className(), ['venue_id' => 'id']);
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
	public function getVenueProvidesSpecialNeeds()
	{
		return $this->hasMany(VenueProvidesSpecialNeeds::className(), ['venue_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getSpecialNeeds()
	{
		return $this->hasMany(SpecialNeeds::className(), ['id' => 'special_needs_id'])
			->viaTable('venue_provides_special_needs', ['venue_id' => 'id']);
	}

}
