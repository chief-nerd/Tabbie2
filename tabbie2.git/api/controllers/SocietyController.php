<?php
/**
 * MotionController.php File
 *
 * @package  Tabbie2
 * @author   jareiter
 * @version     1
 */

namespace api\controllers;

use api\models\Society;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;
use Yii;

class SocietyController extends BaseRestController
{
	public $modelClass = 'api\models\Society';

	public function actions()
	{
		$actions = parent::actions();

		// disable the "delete" and "create" actions
		unset($actions['delete'], $actions['create'], $actions['update']);

		return $actions;
	}

	public function actionSearch($country = null, $abr = null)
	{
		return new ActiveDataProvider([
			'query' => Society::find()
				->joinWith("country")
				->where(["country.name" => $country])
				->orWhere(["country.alpha_2" => $country])
				->orWhere(["country.alpha_3" => $country])
				->orWhere(["society.abr" => $abr])
		]);
	}
}