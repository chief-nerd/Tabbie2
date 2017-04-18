<?php

namespace api\models;


use Yii;
use yii\web\Link;
use yii\helpers\Url;
use yii\web\Linkable;

/**
 * Class Round
 * @package api\models
 */
class Round extends \common\models\Round implements Linkable
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
			$fields['type'],
            $fields['level'],
            $fields['energy'],
            $fields['published'],
            $fields['displayed'],
            $fields['lastrun_temp'],
            $fields['time'], // This field doesn't seem to hold data about the debate but when the model was changed?
            $fields['finished_time']
		);

		return $fields;
	}

	/**
	 * @return array
	 */
	public function getLinks()
	{
		$links = [
			Link::REL_SELF => [
				"api" => Url::to(['round/view', "id" => $this->id], true),
				"web" => Yii::$app->urlManagerFrontend->createAbsoluteUrl(['round/view', "id" => $this->id])
			],
		];

		return $links;
	}

}
