<?php
/**
 * MotionController.php File
 *
 * @package     Tabbie2
 * @author      jareiter
 * @version     1
 */

namespace api\controllers;

use api\models\Tournament;
use Yii;
use api\models\User;
use common\models\LoginForm;
use yii\data\ActiveDataProvider;

/**
 * Class UserController
 * @package api\controllers
 */
class UserController extends BaseRestController
{
	/**
	 * @inheritdoc
	 */
	public $modelClass = 'api\models\User';

	/**
	 * Return the allowed action for this object
	 * @return array
	 */
	public function actions()
	{
		$actions = parent::actions();

		// disable the "delete" and "create" actions
		unset($actions['delete'], $actions['index'], $actions['create'], $actions['update']);

		return $actions;
	}

	/**
	 * Returns the self Identity
	 * @return null|static
	 */
	public function actionMe()
	{
		return $this->redirect(["user/view", "id" => Yii::$app->user->id]);
	}

	/**
	 * @param null $tournament_id
     * @param null $user_id
	 * @return Array
	 */
	public function actionGettournamentrole($user_id = null, $tournament_id = null)
	{
	    if ($user_id != null and $tournament_id != null) {
            $tournament = Tournament::find()
                ->where(["id" => $tournament_id])
                ->one();

            return [
                "tournamentId" => $tournament_id,
                "userId" => $user_id,
                "role" => $tournament->user_role_string((int) $user_id)
            ];
        } else {
	        return [];
        }
	}
}