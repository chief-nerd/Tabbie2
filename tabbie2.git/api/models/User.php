<?php

namespace api\models;


use Yii;
use yii\web\Link;
use yii\helpers\Url;
use yii\web\Linkable;

/**
 * Class User
 * @package api\models
 */
class User extends \common\models\User implements Linkable
{
	/**
	 * Define User fields to publish
	 * @return array
	 */
	public function fields()
	{
		$fields = [
				"name",
				"givenname",
				"surename",
				"picture",
		];

		if(Yii::$app->user->id == $this->id) {
			array_push($fields,
				"url_slug",
				"email",
				"gender",
				"language_status",
				"last_change",
				"language",
				"name");
		}
		return $fields;
	}

	/**
	 * Get related Links
	 * @return array
	 */
	public function getLinks()
	{
		$links = [
			Link::REL_SELF => Url::to(['user/view', "id" => $this->id], true),
		];

		return $links;
	}

}
