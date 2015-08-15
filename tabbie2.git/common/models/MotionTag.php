<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "motion_tag".
 *
 * @property integer        $id
 * @property string         $name
 * @property string         $abr
 * @property-read integer   $count
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
			'id'    => Yii::t('app', 'ID'),
			'name'  => Yii::t('app', 'Name'),
			'abr'   => Yii::t('app', 'Abr'),
			'count' => Yii::t('app', 'Amount'),
		];
	}

	public function fields()
	{
		$fields = parent::fields();

		$fields["count"] = function () {
			return $this->getCount();
		};

		return $fields;
	}

	public function getCount()
	{
		return count($this->tags) + count($this->legacyTags);
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

	public function getOptions()
	{
		$tags = MotionTag::find()->asArray()->all();

		return ArrayHelper::map($tags, "id", "name");
	}
}
