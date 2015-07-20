<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "image".
 *
 * @property integer $id
 * @property integer $size
 * @property string  $path
 * @property string  $uploaded
 * @property integer $is_S3
 * @property integer $parent_image_id
 *
 * @property Image   $parentImage
 * @property Image[] $images
 */
class Image extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'image';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['id', 'path', 'parent_image_id'], 'required'],
			[['id', 'size', 'is_S3', 'parent_image_id'], 'integer'],
			[['uploaded'], 'safe'],
			[['path'], 'string', 'max' => 255],
			[['parent_image_id'], 'exist', 'skipOnError' => true, 'targetClass' => Image::className(), 'targetAttribute' => ['id']],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'              => Yii::t('app', 'ID'),
			'size'            => Yii::t('app', 'Size'),
			'path'            => Yii::t('app', 'Path'),
			'uploaded'        => Yii::t('app', 'Uploaded'),
			'is_S3'           => Yii::t('app', 'Is  S3'),
			'parent_image_id' => Yii::t('app', 'Parent Image ID'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getParentImage()
	{
		return $this->hasOne(Image::className(), ['id' => 'parent_image_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getImages()
	{
		return $this->hasMany(Image::className(), ['parent_image_id' => 'id']);
	}
}
