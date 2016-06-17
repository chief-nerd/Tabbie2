<?php
/**
 * Motion.php File
 *
 * @package  Tabbie2
 * @author   jareiter
 * @version
 */

namespace api\models;

use common\models\LegacyMotion;
use common\models\Round;
use yii\web\Link;
use yii\web\Linkable;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * Class Motion
 * @package api\models
 */
class Motion extends \common\models\Motion implements Linkable
{

	/**
	 * @return array
	 */
	public static function findAll()
	{
		$all = parent::findAll();
		$models = [];
		foreach ($all as $m) {
			$models[] = new Motion($m);
		}

		return $models;
	}

	/**
	 * @return array
	 */
	public function fields()
	{
		$fields = parent::fields();

		// remove fields that contain sensitive information
		unset($fields['object']);

		$fields['tags'] = function ($model) {
			$tags = [];
			foreach ($model->tags as $id => $text) {
				$tags[] = ["text" => $text, "link" => \Yii::$app->urlManagerFrontend->createAbsoluteUrl(["motiontag/view", "id" => $id])];
			}
			return $tags;
		};

		return $fields;
	}

	/**
	 * @return array
	 */
	public function getLinks()
	{
		$links = [
			Link::REL_SELF => [
				"api" => Url::to(['motion/view', "id" => $this->id], true),
				//no web
			],
		];

		if (\Yii::$app->controller->action->id != "index")
			$links["index"] = Url::to(['motion/index'], true);

		return $links;
	}
}