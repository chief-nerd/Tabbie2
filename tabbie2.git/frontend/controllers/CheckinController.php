<?php
/**
 * CheckinController.php File
 *
 * @package  Tabbie2
 * @author   jareiter
 * @version
 */

namespace frontend\controllers;

use common\models;
use common\components\filter\TournamentContextFilter;
use Yii;
use yii\filters\AccessControl;
use frontend\models\CheckinForm;
use common\models\Tournament;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;


class CheckinController extends BaseTournamentController {

	public function behaviors() {
		return [
			'tournamentFilter' => [
				'class' => TournamentContextFilter::className(),
			],
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						'actions' => ['input'],
						'matchCallback' => function ($rule, $action) {
							return $this->_tournament->validateAccessToken(Yii::$app->request->get("accessToken", ""));
						}
					],
					[
						'allow' => true,
						'actions' => ['reset', 'generate-barcodes'],
						'matchCallback' => function ($rule, $action) {
							return (Yii::$app->user->isTabMaster($this->_tournament));
						}
					],
				],
			],
		];
	}

	/**
	 * Show checkin form.
	 *
	 * @return mixed
	 */
	public function actionInput($tournament_id, $number = null, $camInit = false) {

		if (Yii::$app->request->isAjax && $number != null) {
			$model = new CheckinForm([
				"number" => $number
			]);
			$messages = $model->save();
			return Json::encode(["status" => 200, "msg" => $messages]);
		}

		$messages = [];
		$model = new CheckinForm();

		if (Yii::$app->request->isPost) {

			$model->load(Yii::$app->request->post());

			$messages = $model->save();
			$model->number = null;
		}


		return $this->render('input', [
			"model" => $model,
			"tournament" => Tournament::findOne($tournament_id),
			"messages" => $messages,
			"camInit" => $camInit,
		]);
	}

	/**
	 * Show checkin form.
	 *
	 * @return mixed
	 */
	public function actionReset() {

		$rows = models\Team::updateAll(["speakerA_checkedin" => 0, "speakerB_checkedin" => 0], ["tournament_id" => $this->_tournament->id]);
		$rows += models\Adjudicator::updateAll(["checkedin" => 0], ["tournament_id" => $this->_tournament->id]);

		if ($rows > 0)
			Yii::$app->session->addFlash("success", Yii::t("app", "Checking Data reseted"));
		else
			Yii::$app->session->addFlash("info", Yii::t("app", "Already clean"));

		return $this->redirect(["tournament/view", "id" => $this->_tournament->id]);
	}

	public function actionGenerateBarcodes() {

		if (Yii::$app->request->post()) {
			$codes = [];

			$teams = models\Team::find()->tournament($this->_tournament->id)->all();
			$adju = models\Adjudicator::find()->tournament($this->_tournament->id)->all();

			$len_t = strlen($teams[0]->id) + 1;
			$len_a = strlen($adju[0]->id) + 1;

			for ($i = 0; $i < count($teams); $i++) {
				if ($teams[$i]->speakerA) {
					$codes[] = [
						"id" => CheckinForm::TEAMA . "-" . str_pad($teams[$i]->id, $len_t, "0", STR_PAD_LEFT),
						"label" => $teams[$i]->speakerA->name
					];
				}
				if ($teams[$i]->speakerB) {
					$codes[] = [
						"id" => CheckinForm::TEAMB . "-" . str_pad($teams[$i]->id, $len_t, "0", STR_PAD_LEFT),
						"label" => $teams[$i]->speakerB->name
					];
				}
			}

			for ($i = 0; $i < count($adju); $i++) {
				$codes[] = [
					"id" => CheckinForm::ADJU . "-" . str_pad($adju[$i]->id, $len_a, "0", STR_PAD_LEFT),
					"label" => $adju[$i]->user->name
				];
			}

			return $this->renderAjax("barcodes", [
				"codes" => $codes,
				"tournament" => $this->_tournament,
			]);
		}

		return $this->render("barcode_select");
	}
}