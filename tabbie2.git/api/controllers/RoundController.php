<?php
/**
 * RoundController.php File
 *
 * @package  Tabbie2
 * @author   wanaryytel
 * @version     1
 */

namespace api\controllers;
use api\models\Round;
use yii\data\ActiveDataProvider;

/**
 * Class RoundController
 * @package api\controllers
 */
class RoundController extends BaseRestController
{
	/**
	 * @inheritdoc
	 */
	public $modelClass = 'api\models\Round';

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
	 * @param null $tournament_id
	 * @return ActiveDataProvider
	 */
	public function actionFilter($tournament_id = null)
	{
		return new ActiveDataProvider([
			'query' => Round::find()
                ->where(["round.tournament_id" => $tournament_id])
                ->andWhere(["round.published" => 1])
                ->andWhere(["round.displayed" => 1])
		]);
	}
}