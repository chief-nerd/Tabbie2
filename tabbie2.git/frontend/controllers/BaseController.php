<?php

namespace frontend\controllers;

use common\models\User;
use Yii;
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
}
