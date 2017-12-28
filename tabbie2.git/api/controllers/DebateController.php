<?php
/**
 * DebateController.php File
 *
 * @package  Tabbie2
 * @author   wanaryytel
 * @version     1
 */

namespace api\controllers;
use api\models\Debate;
use yii\data\ActiveDataProvider;

/**
 * Class DebateController
 * @package api\controllers
 */
class DebateController extends BaseRestController
{
	/**
	 * @inheritdoc
	 */
	public $modelClass = 'api\models\Debate';

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
	 * @param null $round_id
	 * @return ActiveDataProvider
	 */
	public function actionFilter($round_id = null)
	{
		return new ActiveDataProvider([
			'query' => Debate::find()
                ->where(["debate.round_id" => $round_id])
		]);
	}
}