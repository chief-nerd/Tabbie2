<?php

namespace common\components\filter;

use Yii;
use yii\base\ActionFilter;

class UserContextFilter extends ActionFilter {

	public function beforeAction($action) {
		$user = null;
		$user_id = null;
		if (isset($_GET["user_id"]) || (isset($_GET["id"]))) {

			$user_identifier = (int)Yii::$app->getRequest()->getQueryParam("user_id", null);
			if ($user_identifier == null && $action->controller->id == "user")
				$user_identifier = (int)Yii::$app->getRequest()->getQueryParam("id", null);

			if ($action->controller->hasMethod('_setContext') && $action->controller->hasMethod('_getContext') && $user_identifier > 0) {
				$action->controller->_setContext($user_identifier);
				return true;
			}
			else {
				\Yii::error("Controller " . $action->controller->id . "/" . $action->id . " failed with user_identifier=" . $user_identifier . "\n GET:" . print_r($_GET, true));
				throw new \yii\web\HttpException(500, 'This filter was not properly setup');
			}
		}
		if ($action->controller->id == "user" || $action->id == "list") {
			Yii::trace("Context Controller = user", __METHOD__);
			return true;
		}
		throw new \yii\web\HttpException(500, 'This filter was wronly applied, id missing');
	}

}
