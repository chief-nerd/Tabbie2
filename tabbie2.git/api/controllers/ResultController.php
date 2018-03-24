<?php
/**
 * ResultController.php File
 *
 * @package  Tabbie2
 * @author   wanaryytel
 * @version     1
 */

namespace api\controllers;
use api\models\Result;
use yii\data\ActiveDataProvider;

/**
 * Class RoundController
 * @package api\controllers
 */
class ResultController extends BaseRestController
{
	/**
	 * @inheritdoc
	 */
	public $modelClass = 'api\models\Result';

	/**
	 * Return the allowed action for this object
	 * @return array
	 */
	public function actions()
	{
		$actions = parent::actions();

		// disable the actions we don't want
		unset($actions['delete'], $actions['index'], $actions['create'], $actions['update']);

		return $actions;
	}

	/**
	 * @param null $tournament_id
	 * @return ActiveDataProvider
	 */
	public function actionFilter($tournament_id = null, $round_id = null)
	{
		return new ActiveDataProvider([
			'query' => Result::find()
				->joinWith('debate')
				->where(['debate.tournament_id' => $tournament_id])
				->andWhere(["debate.round_id" => $round_id])
		]);
	}
}