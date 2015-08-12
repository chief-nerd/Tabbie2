<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tag".
 *
 * @property integer   $motion_tag_id
 * @property integer   $round_id
 *
 * @property MotionTag $motionTag
 * @property Round     $round
 */
class Tag extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'tag';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['motion_tag_id', 'round_id'], 'required'],
			[['motion_tag_id', 'round_id'], 'integer'],
			//[['motion_tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => MotionTag::className(), 'targetAttribute' => ['motion_tag_id' => 'id']],
			//[['round_id'], 'exist', 'skipOnError' => true, 'targetClass' => Round::className(), 'targetAttribute' => ['round_id' => 'id']],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'motion_tag_id' => Yii::t('app', 'Motion Tag ID'),
			'round_id'      => Yii::t('app', 'Round ID'),
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
	public function getRound()
	{
		return $this->hasOne(Round::className(), ['id' => 'round_id']);
	}
}
