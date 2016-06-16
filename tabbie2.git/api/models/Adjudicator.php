<?php

namespace api\models;

use Yii;
use yii\web\Link;
use yii\helpers\Url;
use yii\web\Linkable;

/**
 * Class Adjudicator
 * @package api\models
 */
class Adjudicator extends \common\models\Adjudicator implements Linkable
{
	/**
	 * @return array
	 */
	public function fields()
	{
		$fields = [
			"id",
			'strength',
		];

		$fields['name'] = function ($model) {
			return $model->user->name;
		};

		$fields['society'] = function ($model) {
			return $model->society->fullname;
		};

		return $fields;
	}

	/**
	 * @return array
	 */
	public function getLinks()
	{
		$links = [
			Link::REL_SELF => Url::to(['adjudicator/view', "id" => $this->id], true),
			'self_web' => Yii::$app->urlManagerFrontend->createAbsoluteUrl([
				'adjudicator/view',
				"id" => $this->id,
				"tournament_id"=> $this->tournament_id
			]),
			'user' => Url::to(['user/view', 'id' => $this->user_id], true),
			'society' => Url::to(['society/view', 'id' => $this->society_id], true),
		];

		return $links;
	}

}
