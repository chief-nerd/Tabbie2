<?php
/**
 * MotionController.php File
 *
 * @package  Tabbie2
 * @author   jareiter
 * @version     1
 */

namespace api\controllers;


class TournamentController extends BaseRestController
{
	public $modelClass = 'api\models\Tournament';

	public function actions()
	{
		$actions = parent::actions();

		// disable the "delete" and "create" actions
		unset($actions['delete'], $actions['create'], $actions['update']);

		return $actions;
	}
}