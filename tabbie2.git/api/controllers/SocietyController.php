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

/**
 * Class SocietyController
 * @package api\controllers
 */
class SocietyController extends BaseRestController
{
	/**
	 * @inheritdoc
	 */
	public $modelClass = 'api\models\Society';

	/**
	 * Return the allowed action for this object
	 * @return array
	 */
	public function actions()
	{
		$actions = parent::actions();

		// disable the "delete" and "create" actions
		unset($actions['delete'], $actions['create'], $actions['update']);

		return $actions;
	}

	/**
	 * @param null $country
	 * @param null $abr
	 * @return ActiveDataProvider
	 */
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