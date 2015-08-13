<?php

namespace common\models;

use common\components\ObjectError;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "legacy_motion".
 *
 * @property integer     $id
 * @property string      $motion
 * @property string      $language
 * @property string      $time
 * @property string      $infoslide
 * @property string      $tournament
 * @property string      $round
 * @property string      $link
 * @property integer     $by_user_id
 *
 * @property User        $byUser
 * @property LegacyTag[] $legacyTags
 * @property MotionTag[] $motionTags
 */
class LegacyMotion extends \yii\db\ActiveRecord
{
	public $_tags = [];

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'legacy_motion';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['motion', 'time', 'tournament'], 'required'],
			[['id', 'by_user_id'], 'integer'],
			[['motion', 'infoslide', 'language', 'round'], 'string'],
			[['time', 'tags'], 'safe'],
			['link', 'url'],
			[['tournament'], 'string', 'max' => 255],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'         => Yii::t('app', 'ID'),
			'motion'     => Yii::t('app', 'Motion'),
			'language'   => Yii::t("app", 'Language'),
			'time'       => Yii::t('app', 'Date'),
			'infoslide'  => Yii::t('app', 'Infoslide'),
			'tournament' => Yii::t('app', 'Tournament'),
			'link'       => Yii::t('app', 'Link'),
			'by_user_id' => Yii::t('app', 'By User ID'),
			'tags'       => Yii::t("app", 'Motion Tags'),
		];
	}

	public function getTags()
	{
		return ArrayHelper::getColumn($this->motionTags, "id");
	}

	public function setTags($value)
	{
		$this->_tags = $value;
	}

	public function saveTags()
	{
		if (is_int($this->id))
			LegacyTag::deleteAll(["legacy_motion_id" => $this->id]);
		foreach ($this->_tags as $t) {
			if (!is_numeric($t)) {
				$new_Tag = new MotionTag([
					"name" => ucwords(htmlentities(trim($t))),
					"abr"  => null,
				]);
				if (!$new_Tag->save())
					Yii::error("Save new_Tag error", ObjectError::getMsg($new_Tag));
				$t = $new_Tag->id;
			}

			$tag = new LegacyTag([
				"motion_tag_id"    => $t,
				"legacy_motion_id" => $this->id,
			]);
			if (!$tag->save()) {
				Yii::error("Save tag error", ObjectError::getMsg($tag));
			}
		}

		return true;
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getByUser()
	{
		return $this->hasOne(User::className(), ['id' => 'by_user_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getLegacyTags()
	{
		return $this->hasMany(LegacyTag::className(), ['legacy_motion_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getMotionTags()
	{
		return $this->hasMany(MotionTag::className(), ['id' => 'motion_tag_id'])->viaTable('legacy_tag', ['legacy_motion_id' => 'id']);
	}
}
