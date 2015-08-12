<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "motion_tag".
 *
 * @property integer        $id
 * @property string         $name
 * @property string         $abr
 *
 * @property LegacyTag[]    $legacyTags
 * @property LegacyMotion[] $legacyMotions
 * @property Tag[]          $tags
 * @property Round[]        $rounds
 */
class MotionTag extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'motion_tag';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['name'], 'required'],
			[['name'], 'string', 'max' => 255],
			[['abr'], 'string', 'max' => 100],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'   => Yii::t('app', 'ID'),
			'name' => Yii::t('app', 'Name'),
			'abr'  => Yii::t('app', 'Abr'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getLegacyTags()
	{
		return $this->hasMany(LegacyTag::className(), ['motion_tag_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getLegacyMotions()
	{
		return $this->hasMany(LegacyMotion::className(), ['id' => 'legacy_motion_id'])->viaTable('legacy_tag', ['motion_tag_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTags()
	{
		return $this->hasMany(Tag::className(), ['motion_tag_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getRounds()
	{
		return $this->hasMany(Round::className(), ['id' => 'round_id'])->viaTable('tag', ['motion_tag_id' => 'id']);
	}
}
