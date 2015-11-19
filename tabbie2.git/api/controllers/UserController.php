<?php
/**
 * MotionController.php File
 *
 * @package     Tabbie2
 * @author      jareiter
 * @version     1
 */

namespace api\controllers;


use api\models\User;

class UserController extends BaseRestController
{
	public $modelClass = 'api\models\User';

	public function actions()
	{
		$actions = parent::actions();

		// disable the "delete" and "create" actions
		unset($actions['delete'], $actions['index'], $actions['create'], $actions['update']);

		return $actions;
	}

	public function actionMe()
	{
		return User::findIdentityByAccessToken(\Yii::$app->request->get("access-token"));
	}
}