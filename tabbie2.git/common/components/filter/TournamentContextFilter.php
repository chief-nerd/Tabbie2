<?php

namespace common\components\filter;

use Yii;
use yii\base\ActionFilter;

class TournamentContextFilter extends ActionFilter
{

	public function beforeAction($action)
	{
		$tournament = null;
		$tournamnet_id = null;
		if (isset($_GET["tournament_id"]) || (isset($_GET["id"]))) {
			$tournamnet_identifier = (int)Yii::$app->getRequest()->getQueryParam("tournament_id", null);
			if ($tournamnet_identifier == null && $action->controller->id == "tournament")
				$tournamnet_identifier = (int)Yii::$app->getRequest()->getQueryParam("id", null);

			if ($action->controller->hasMethod('_setContext') && $action->controller->hasMethod('_getContext') && $tournamnet_identifier > 0) {
				$action->controller->_setContext($tournamnet_identifier);

				return true;
			} else {
				\Yii::error("Controller " . $action->controller->id . "/" . $action->id . " failed with tournamnet_identifier=" . $tournamnet_identifier . "\n GET:" . print_r($_GET, true));
				throw new \yii\web\HttpException(500, 'This filter was not properly setup');
			}
		}
		if ($action->controller->id == "tournament") {
			Yii::trace("Context Controller = tournament", __METHOD__);

			return true;
		}
		throw new \yii\web\HttpException(500, 'This filter was wronly applied, id missing');
	}

}
