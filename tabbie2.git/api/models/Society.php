<?php

namespace api\models;


use Yii;
use yii\web\Link;
use yii\helpers\Url;
use yii\web\Linkable;

/**
 * Class Society
 * @package api\models
 */
class Society extends \common\models\Society implements Linkable
{
	/**
	 * @return array
	 */
	public function extraFields()
	{
		$fields = $this->fields();


		return $fields;
	}

	/**
	 * @return array
	 */
	public function fields()
	{
		$fields = parent::fields();

		// remove fields that contain sensitive information
		unset(
			$fields['country_id']
		);

		$fields['country'] = function ($model) {
			return $model->country->name;
		};

		return $fields;
	}

	/**
	 * @return array
	 */
	public function getLinks()
	{
		$links = [
			Link::REL_SELF => Url::to(['society/view', "id" => $this->id], true),
			//'self_web' => Yii::$app->urlManagerFrontend->createAbsoluteUrl(['tournament/view', "id" => $this->id]),
		];

		if (Yii::$app->controller->action->id != "index")
			$links["index"] = Url::to(['society/index'], true);

		return $links;
	}

}
