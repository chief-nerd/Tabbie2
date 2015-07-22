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
use kartik\mpdf\Pdf;
use Yii;
use yii\filters\AccessControl;
use frontend\models\CheckinForm;
use common\models\Tournament;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\UploadedFile;


class CheckinController extends BaseTournamentController
{

	public function behaviors()
	{
		return [
			'tournamentFilter' => [
				'class' => TournamentContextFilter::className(),
			],
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow'   => true,
						'actions' => ['input'],
						'matchCallback' => function ($rule, $action) {
							return $this->_tournament->validateAccessToken(Yii::$app->request->get("accessToken", ""));
						}
					],
					[
						'allow'         => true,
						'actions'       => ['generate-barcodes', 'generate-badges'],
						'matchCallback' => function ($rule, $action) {
							return (
								$this->_tournament->isTabMaster(Yii::$app->user->id) ||
								$this->_tournament->isConvenor(Yii::$app->user->id) ||
								$this->_tournament->validateAccessToken(Yii::$app->request->get("accessToken", ""))
							);
						}
					],
					[
						'allow'         => true,
						'actions'       => ['reset'],
						'matchCallback' => function ($rule, $action) {
							return (
								$this->_tournament->isTabMaster(Yii::$app->user->id) ||
								$this->_tournament->isConvenor(Yii::$app->user->id)
							);
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
	public function actionInput($tournament_id, $number = null, $camInit = false)
	{

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
			"model"    => $model,
			"tournament" => Tournament::findOne($tournament_id),
			"messages" => $messages,
			"camInit"  => $camInit,
		]);
	}

	/**
	 * Show checkin form.
	 *
	 * @return mixed
	 */
	public function actionReset()
	{

		$rows = models\Team::updateAll(["speakerA_checkedin" => 0, "speakerB_checkedin" => 0], ["tournament_id" => $this->_tournament->id]);
		$rows += models\Adjudicator::updateAll(["checkedin" => 0], ["tournament_id" => $this->_tournament->id]);

		if ($rows > 0)
			Yii::$app->session->addFlash("success", Yii::t("app", "Checking Data reseted"));
		else
			Yii::$app->session->addFlash("info", Yii::t("app", "Already clean"));

		return $this->redirect(["tournament/view", "id" => $this->_tournament->id]);
	}

	public function actionGenerateBarcodes()
	{

		if (Yii::$app->request->post()) {
			$offset = Yii::$app->request->post("offset", 0);
			$codes = [];

			$userID = Yii::$app->request->post("userID", null);

			$teams = models\Team::find()->tournament($this->_tournament->id)->all();
			$adju = models\Adjudicator::find()->tournament($this->_tournament->id)->all();

			$len_t = strlen($teams[0]->id) + 1;
			$len_a = strlen($adju[0]->id) + 1;

			for ($i = 0; $i < count($teams); $i++) {
				if ($teams[$i]->speakerA) {
					if ($userID == null || $userID == $teams[$i]->speakerA_id)
						$codes[] = [
							"id" => CheckinForm::TEAMA . "-" . str_pad($teams[$i]->id, $len_t, "0", STR_PAD_LEFT),
							"label" => $teams[$i]->speakerA->name
						];
				}
				if ($teams[$i]->speakerB) {
					if ($userID == null || $userID == $teams[$i]->speakerA_id)
						$codes[] = [
							"id"    => CheckinForm::TEAMB . "-" . str_pad($teams[$i]->id, $len_t, "0", STR_PAD_LEFT),
							"label" => $teams[$i]->speakerB->name
						];
				}
			}

			for ($i = 0; $i < count($adju); $i++) {
				if ($userID == null || $userID == $adju[$i]->user_id)
					$codes[] = [
						"id"    => CheckinForm::ADJU . "-" . str_pad($adju[$i]->id, $len_a, "0", STR_PAD_LEFT),
						"label" => $adju[$i]->user->name
					];
			}

			return $this->renderAjax("barcodes", [
				"codes"  => $codes,
				"tournament" => $this->_tournament,
				"offset" => $offset,
			]);
		}

		return $this->render("barcode_select");
	}

	public function actionGenerateBadges()
	{

		if (Yii::$app->request->post()) {

			$new_file = UploadedFile::getInstanceByName("badge");
			if ($new_file instanceof UploadedFile) {
				$this->_tournament->saveBadge($new_file);
				$this->_tournament->save();
			}

			$teams = models\Team::find()->tournament($this->_tournament->id)->all();
			$a_teams = [];
			$adju = models\Adjudicator::find()->tournament($this->_tournament->id)->all();
			$a_adjus = [];

			if (count($teams) > 0) {
				$len_t = strlen($teams[0]->id);
				if ($teams[0]->id[0] > 7) $len_t++;

				for ($i = 0; $i < count($teams); $i++) {
					$a_teams[$i] = $teams[$i]->attributes;
					$a_teams[$i]["society"] = $teams[$i]->society->fullname;

					if ($teams[$i]->speakerA) {
						$a_teams[$i]["A"] = [
							"code" => CheckinForm::TEAMA . "-" . str_pad($teams[$i]->id, $len_t, "0", STR_PAD_LEFT),
							"name" => $teams[$i]->speakerA->name
						];
					}
					if ($teams[$i]->speakerB) {
						$a_teams[$i]["B"] = [
							"code" => CheckinForm::TEAMB . "-" . str_pad($teams[$i]->id, $len_t, "0", STR_PAD_LEFT),
							"name" => $teams[$i]->speakerB->name
						];
					}
				}
			}
			if (count($adju) > 0) {
				$len_a = strlen($adju[0]->id) + 1;
				for ($i = 0; $i < count($adju); $i++) {
					$a_adjus[$i] = array_merge($adju[$i]->attributes, [
						"code" => CheckinForm::ADJU . "-" . str_pad($adju[$i]->id, $len_a, "0", STR_PAD_LEFT),
						"name" => $adju[$i]->user->name
					]);
					$a_adjus[$i]["society"] = $adju[$i]->society->fullname;

				}
			}

			if (Yii::$app->request->post("use", false))
				$badgeURL = $this->_tournament->getBadge();
			else
				$badgeURL = "";

			$setting["A6"] = [
				"format" => 'A6',
				"margin" => Yii::$app->request->post("margin", 0),
				"style"  => '@frontend/assets/css/badge.css',
				"css"    => ".paper { width: 100%; height: 100%;} .badge { width: 50%; height: 100%; }",
			];

			$setting["A4"] = [
				"format" => 'A4',
				"margin" => Yii::$app->request->post("margin", 0),
				"style"  => '@frontend/assets/css/badge.css',
				//"css" => ".paper { width: 14.8cm; height: 10.5cm;} .badge { width: 7.4cm; height: 10.5cm }",
				"css"    => ".paper { width: 50%; height: 50%;} .badge { width: 50%; height: 50%; }",
			];

			$paper = Yii::$app->request->post("paper", false);
			if ($paper)
				$set = $setting[$paper];
			else
				$set = $setting["A6"];

			$pdf = new Pdf([
				'mode'         => Pdf::MODE_BLANK, // leaner size using standard fonts
				'format'       => $set["format"],
				'orientation'  => Pdf::ORIENT_LANDSCAPE,
				'cssInline'    => $set["css"],
				'cssFile'      => '@frontend/assets/css/badge.css',
				'content'      => $this->renderPartial("badges", [
					"teams"      => $a_teams,
					"adjus"      => $a_adjus,
					"tournament" => $this->_tournament,
					"backurl"    => $badgeURL,
				]),
				"marginLeft"   => $set["margin"],
				"marginTop"    => $set["margin"],
				"marginRight"  => $set["margin"],
				"marginBottom" => $set["margin"],
				"marginHeader" => 0,
				"marginFooter" => 0,
				'options'      => [
					'title' => 'Badgets for ' . $this->_tournament->name,
				],
			]);

			$mpdf = $pdf->getApi();

			return $pdf->render();
		}

		return $this->render("badge_select");
	}
}