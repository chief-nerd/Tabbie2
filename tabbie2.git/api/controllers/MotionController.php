<?php
/**
 * MotionController.php File
 *
 * @package  Tabbie2
 * @author   jareiter
 * @version     1
 */

namespace api\controllers;

use api\models\Motion;
use yii\data\ArrayDataProvider;

/**
 * Class MotionController
 * @package api\controllers
 */
class MotionController extends BaseRestController
{
	/**
	 * @inheritdoc
	 */
	public $modelClass = 'api/models/Motion';

	/**
	 * Return the allowed action for this object
	 * @return array
	 */
	public function actions()
	{
		$actions = parent::actions();

		// disable the "delete" and "create" actions
		unset($actions['delete'], $actions['create'], $actions['update']);
		//unset($actions['view']);

		// customize the data provider preparation with the "prepareDataProvider()" method
		$actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
		$actions['view']['findModel'] = [$this, 'findModel'];

		return $actions;
	}

	/**
	 * @return ArrayDataProvider
	 */
	public function prepareDataProvider()
	{
		$models = Motion::findAll();

		// prepare and return a data provider for the "index" action
		return new ArrayDataProvider([
			'allModels' => $models
		]);
	}

	/**
	 * @param $id
	 * @return \common\models\Motion
	 * @throws \yii\web\NotFoundHttpException
	 */
	public function findModel($id)
	{
		return Motion::findOne($id);
	}
}