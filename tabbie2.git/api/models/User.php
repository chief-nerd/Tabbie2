<?php

namespace api\models;


use Yii;
use yii\web\Link;
use yii\helpers\Url;
use yii\web\Linkable;


class User extends \common\models\User implements Linkable
{
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

	public function getLinks()
	{
		$links = [
			Link::REL_SELF => Url::to(['user/view', "id" => $this->id], true),
		];

		return $links;
	}

}
