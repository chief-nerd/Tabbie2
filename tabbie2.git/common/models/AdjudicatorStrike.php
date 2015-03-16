<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "adjudicator_strike".
 *
 * @property integer     $adjudicator_id
 * @property integer     $adjudicator_id1
 * @property Adjudicator $adjudicator
 * @property Adjudicator $adjudicatorId1
 */
class AdjudicatorStrike extends \yii\db\ActiveRecord {
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'adjudicator_strike';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['adjudicator_id', 'adjudicator_id1'], 'required'],
			[['adjudicator_id', 'adjudicator_id1'], 'integer']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'adjudicator_id' => Yii::t('app', 'Adjudicator ID'),
			'adjudicator_id1' => Yii::t('app', 'Adjudicator Id1'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getAdjudicator() {
		return $this->hasOne(Adjudicator::className(), ['id' => 'adjudicator_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getAdjudicatorId1() {
		return $this->hasOne(Adjudicator::className(), ['id' => 'adjudicator_id1']);
	}
}
