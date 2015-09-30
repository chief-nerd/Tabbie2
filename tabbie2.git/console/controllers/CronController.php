<?php
/**
 * Created by IntelliJ IDEA.
 * User: jareiter
 * Date: 29/09/15
 * Time: 15:46
 */

namespace console\controllers;

use common\models\Tournament;
use yii\console\Controller;

/**
 * Regular Cronjob Controller
 *
 * Class CronController
 * @package console\controllers
 */
class CronController extends Controller {

	public $defaultAction = "all";

	/**
	 * Execute ALL cronjobs
	 */
	public function actionAll() {
		foreach (get_class_methods(self::className()) as $function) {
			if (strpos($function, "action") === 0 && $function != __FUNCTION__) {
				\call_user_func([self::className(), $function]);
			}
		}
	}

	/**
	 * Sends a feedback request to past tournaments that have been unused
	 */
	public function actionEmptyTournamentFeedback() {
		$tournaments = Tournament::find()
			->where("end_date < :end", ["end" => date("Y-m-d H:i:s", strtotime("-3 day"))])
			->all();

		foreach ($tournaments as $model) {
			if (count($model->teams) == 0 || count($model->adjudicators) == 0) {

			}
		}
	}

	/**
	 * Send an Email to all tournaments that are about to happen to ask for help
	 */
	public function actionNeedHelpInAdvanceMail() {

	}
}