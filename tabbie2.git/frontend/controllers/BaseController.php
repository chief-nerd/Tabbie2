<?php

namespace frontend\controllers;

use common\models\User;
use Yii;
use yii\base\View;
use yii\web\Controller;

/**
 * Tabbie2 Master Base Controller
 *
 * @package frontend\controllers
 */
class BaseController extends Controller
{
	public function init()
	{
		parent::init();
		if (!Yii::$app->user->isGuest)
			Yii::$app->language = Yii::$app->user->language;
	}

	public function beforeAction($action)
	{
		Yii::$app->view->on(View::EVENT_BEGIN_PAGE, function () {
			$view = Yii::$app->controller->view;
			$view->registerMetaTag(["name" => "apple-mobile-web-app-capable", "content" => "yes"], "apple-mobile-web-app-capable");
		});
		return parent::beforeAction($action);
	}
}
