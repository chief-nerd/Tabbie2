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

class MotionController extends BaseRestController
{
	public $modelClass = 'api/models/Motion';

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

	public function prepareDataProvider()
	{
		$models = Motion::findAll();

		// prepare and return a data provider for the "index" action
		return new ArrayDataProvider([
			'allModels' => $models
		]);
	}

	public function findModel($id)
	{
		return Motion::findOne($id);
	}
}