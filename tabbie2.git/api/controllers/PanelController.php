<?php
/**
 * MotionController.php File
 *
 * @package     Tabbie2
 * @author      jareiter
 * @version     1
 */

namespace api\controllers;

/**
 * Class PanelController
 * @package api\controllers
 */
class PanelController extends BaseRestController
{
	/**
	 * @inheritdoc
	 */
	public $modelClass = 'api\models\Panel';

	/**
	 * Return the allowed action for this object
	 * @return array
	 */
	public function actions()
	{
		$actions = parent::actions();

		// disable the "delete" and "create" actions
		unset($actions['delete'], $actions['index'], $actions['create']);

		return $actions;
	}
}