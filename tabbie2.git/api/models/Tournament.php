<?php

namespace api\models;


use Yii;
use yii\web\Link;
use yii\helpers\Url;
use yii\web\Linkable;


class Tournament extends \common\models\Tournament implements Linkable
{
	public function extraFields()
	{
		$fields = $this->fields();

		$fields['hosted_by'] = function ($model) {
			return $model->hostedby;
		};

		$fields['tabmaster'] = function ($model) {
			/** Tournament $model */
			$tabs = [];
			foreach ($model->tabmasters as $t) {
				$tabs[] = new User($t);
			}
			return $tabs;
		};

		return $fields;
	}

	public function fields()
	{
		$fields = parent::fields();

		// remove fields that contain sensitive information
		unset(
			$fields['tabAlgorithmClass'],
			$fields['accessToken'],
			$fields['url_slug'],
			$fields['hosted_by_id'],
			$fields['time']
		);

		$fields['hosted_by'] = function ($model) {
			return $model->hostedby->fullname;
		};

		$fields['status'] = function ($model) {
			return self::getStatusLabel($model->status);
		};

		$fields['logo'] = function ($model) {
			return $model->getLogo(true, Yii::$app->urlManagerFrontend);
		};

		$fields['convenor'] = function ($model) {
			/** Tournament $model */
			$con = [];
			foreach ($model->convenors as $t) {
				$con[] = $t->name;
			}
			return $con;
		};

		$fields['ca'] = function ($model) {
			/** Tournament $model */
			$cas = [];
			foreach ($model->cAs as $t) {
				$cas[] = $t->name;
			}
			return $cas;
		};

		$fields['tabmaster'] = function ($model) {
			/** Tournament $model */
			$tabs = [];
			foreach ($model->tabmasters as $t) {
				$tabs[] = $t->name;
			}
			return $tabs;
		};

		return $fields;
	}

	public function getLinks()
	{
		$links = [
			Link::REL_SELF => Url::to(['tournament/view', "id" => $this->id], true),
			'self_web' => Yii::$app->urlManagerFrontend->createAbsoluteUrl(['tournament/view', "id" => $this->id]),
			'hosted_by' => Url::to(['society/view', "id" => $this->hosted_by_id], true),
		];

		if (Yii::$app->controller->action->id != "index")
			$links["index"] = Url::to(['tournament/index'], true);

		return $links;
	}

}
