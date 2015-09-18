<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "legacy_tag".
 *
 * @property integer      $motion_tag_id
 * @property integer      $legacy_motion_id
 *
 * @property MotionTag    $motionTag
 * @property LegacyMotion $legacyMotion
 */
class LegacyTag extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'legacy_tag';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['motion_tag_id', 'legacy_motion_id'], 'required'],
			[['motion_tag_id', 'legacy_motion_id'], 'integer'],
			//[['motion_tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => MotionTag::className(), 'targetAttribute' => ['motion_tag_id' => 'id']],
			//[['legacy_motion_id'], 'exist', 'skipOnError' => true, 'targetClass' => LegacyMotion::className(), 'targetAttribute' => ['legacy_motion_id' => 'id']],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'motion_tag_id'    => Yii::t('app', 'Motion Tag') . ' ' . Yii::t('app', 'ID'),
			'legacy_motion_id' => Yii::t('app', 'Legacy Motion') . ' ' . Yii::t('app', 'ID'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getMotionTag()
	{
		return $this->hasOne(MotionTag::className(), ['id' => 'motion_tag_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getLegacyMotion()
	{
		return $this->hasOne(LegacyMotion::className(), ['id' => 'legacy_motion_id']);
	}
}
