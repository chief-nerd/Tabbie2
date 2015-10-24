<?php

namespace frontend\controllers;

use common\components\filter\TournamentContextFilter;
use common\components\ObjectError;
use common\models\Adjudicator;
use common\models\AdjudicatorInPanel;
use common\models\Debate;
use common\models\DrawLine;
use common\models\Panel;
use common\models\Result;
use common\models\Round;
use common\models\search\DebateSearch;
use common\models\Team;
use kartik\mpdf\Pdf;
use mPDF;
use Yii;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * RoundController implements the CRUD actions for Round model.
 */
class RoundController extends BasetournamentController
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
						'actions' => ['index', 'view', 'update', 'printballots', 'debatedetails', 'changevenue', 'switch-adjudicators'],
						'matchCallback' => function ($rule, $action) {
							return (
								$this->_tournament->isTabMaster(Yii::$app->user->id) ||
								$this->_tournament->isConvenor(Yii::$app->user->id) ||
								$this->_tournament->isCA(Yii::$app->user->id)
							);
						}
					],
					[
						'allow'   => true,
						'actions' => ['create', 'publish', 'redraw', 'improve', 'export', 'import'],
						'matchCallback' => function ($rule, $action) {
							return ($this->_tournament->isTabMaster(Yii::$app->user->id));
						}
					],
				],
			],
		];
	}

	/**
	 * Lists all Round models.
	 *
	 * @return mixed
	 */
	public function actionIndex()
	{
		$dataProvider = new ActiveDataProvider([
			'query' => Round::find()->where(["tournament_id" => $this->_tournament->id]),
		]);

		return $this->render('index', [
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single Round model.
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionView($id)
	{
		$model = $this->findModel($id);

		$debateSearchModel = new DebateSearch();
		$debateDataProvider = $debateSearchModel->search(Yii::$app->request->queryParams, $this->_tournament->id, $id);

		// validate if there is a editable input saved via AJAX
		if (Yii::$app->request->post('hasEditable')) {
			// instantiate your debate model for saving
			$debateID = Yii::$app->request->post('editableKey');
			$debate = \common\models\Debate::findOne($debateID);

			// store a default json response as desired by editable
			$out = \yii\helpers\Json::encode(['output' => '', 'message' => '']);

			// return ajax json encoded response and exit

			return $out;
		}

		return $this->render('view', [
			'model'             => $model,
			'debateSearchModel' => $debateSearchModel,
			'debateDataProvider' => $debateDataProvider,
		]);
	}

	/**
	 * Finds the Round model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 *
	 * @return Round the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		if (($model = Round::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	/**
	 * Function called when move results are sent
	 */
	public function actionChangevenue($id, $debateid)
	{
		$selected_debate = \common\models\Debate::findOne($debateid);

		if ($params = Yii::$app->request->get()) {

			$used_debate = \common\models\Debate::findOne(["venue_id" => $params["new_venue"], "round_id" => $selected_debate->round_id]);
			if ($used_debate instanceof \common\models\Debate) {
				$old_debate_venue = $selected_debate->venue_id;
				$selected_debate->venue_id = $used_debate->venue_id;
				$used_debate->venue_id = $old_debate_venue;
				if ($selected_debate->save() && $used_debate->save()) {
					Yii::$app->session->setFlash('success', Yii::t("app", 'Venues switched'));
				} else {
					Yii::$app->session->setFlash('error', Yii::t("app", 'Error while switching'));
				}
			} else {
				$selected_debate->venue_id = $params["new_venue"];
				if ($selected_debate->save()) {
					Yii::$app->session->setFlash('success', Yii::t("app", 'New Venues set'));
				} else {
					Yii::$app->session->setFlash('error', Yii::t("app", 'Error while setting new venue'));
				}
			}
		}

		return $this->redirect(["view", "id" => $id, "tournament_id" => $selected_debate->tournament_id, "view" => "#draw"]);
	}

	/**
	 * Creates a new Round model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate()
	{
		if (\common\models\Team::find()->active()->tournament($this->_tournament->id)->count() % 4 != 0) {
			\Yii::$app->session->setFlash("error", Yii::t("app", "Can't create Round: Amount of Teams is not dividable by 4"));

			return $this->redirect(["team/index", "tournament_id" => $this->_tournament->id]);
		}

		$model = new Round();
		$model->tournament_id = $this->_tournament->id;
		$model->setNextRound();

		if ($model->type >= Round::TYP_OUT)
			$this->redirect(["outround/create", "tournament_id" => $this->_tournament->id]); //We are already on outround

		if ($model->load(Yii::$app->request->post())) {

			if (!$model->save() || !$model->generateWorkingDraw()) {
				Yii::$app->session->addFlash("error", ObjectError::getMsg($model));
			} else
				return $this->redirect(['round/view', 'id' => $model->id, "tournament_id" => $model->tournament_id]);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing Round model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionUpdate($id)
	{
		$model = $this->findModel($id);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id, "tournament_id" => $model->tournament_id]);
		} else {
			return $this->render('update', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Deletes an existing Round model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionDelete($id)
	{
		$this->findModel($id)->delete();

		return $this->redirect(['index']);
	}

	/**
	 * Publish the Draw
	 *
	 * @param $id
	 *
	 * @return \yii\web\Response
	 * @throws \yii\web\NotFoundHttpException
	 */
	public function actionPublish($id)
	{
		$model = $this->findModel($id);
		$model->published = 1;
		$model->save();

		Panel::updateAll(["used" => 1], ["tournament_id" => $this->_tournament->id]);

		return $this->redirect(['view', 'id' => $model->id, "tournament_id" => $model->tournament_id]);
	}

	public function actionRedraw($id)
	{
		$model = Round::findOne(["id" => $id]);

		if ($model instanceof Round) {

			$canProceed = true;
			foreach ($model->debates as $debate) {
				/** @var Debate $debate */
				if ($debate->result instanceof Result) {
					$canProceed = false;
				}
			}

			if ($canProceed == false) {
				Yii::$app->session->addFlash("warning", Yii::t("app", "Already Results entered for this round. Can't redraw!"));
				$this->redirect(["view", "id" => $id, "tournament_id" => $this->_tournament->id]);
			}

			$time = microtime(true);

			foreach ($model->debates as $debate) {
				/** @var Debate $debate * */
				if (!$debate->panel->is_preset) { //Only delete non-preset panels
					foreach ($debate->panel->adjudicatorInPanels as $aj)
						$aj->delete();

					$panelid = $debate->panel_id;
					$debate->delete();
					Panel::deleteAll(["id" => $panelid]);
				} else {
					$debate->delete();
				}
			}

			if (!$model->generateWorkingDraw()) {
				Yii::$app->session->addFlash("error", ObjectError::getMsg($model));
			} else {
				$model->time = Yii::$app->time->UTC();
				$model->save();
				Yii::$app->session->addFlash("success", Yii::t("app", "Successfully redrawn in {secs}s", ["secs" => intval(microtime(true) - $time)]));
			}
		}

		//return $this->render("debug");
		return $this->redirect(['view', 'id' => $model->id, "tournament_id" => $model->tournament_id]);
	}

	public function actionImprove($id, $runs = null)
	{
		$model = Round::findOne(["id" => $id]);

		if ($model instanceof Round) {

			$canProceed = true;
			foreach ($model->debates as $debate) {
				/** @var Debate $debate */
				if ($debate->result instanceof Result) {
					$canProceed = false;
				}
			}

			if ($canProceed == false) {
				Yii::$app->session->addFlash("warning", Yii::t("app", "Already Results entered for this round. Can't improve!"));
				$this->redirect(["view", "id" => $id, "tournament_id" => $this->_tournament->id]);
			}

			try {
				$time = microtime(true);
				$oldEnergy = $model->energy;
				$model->improveAdjudicator($runs);
				$model->save();
				$diff = ($oldEnergy - $model->energy);
				Yii::$app->session->addFlash(($diff > 0) ? "success" : "info", Yii::t("app", "Improved Energy by {diff} points in {secs}s", [
					"diff" => $diff,
					"secs" => intval(microtime(true) - $time),
				]));

			} catch (Exception $ex) {
				Yii::$app->session->addFlash("error", $ex->getMessage());
			}
		}

		//return $this->render("debug");
		return $this->redirect(['view', 'id' => $model->id, "tournament_id" => $model->tournament_id]);
	}

	public function actionPrintballots($id, $debug = false)
	{
		set_time_limit(0);
		$model = Round::findOne(["id" => $id]);

		$pdf = new Pdf([
			'mode'    => Pdf::MODE_UTF8, // leaner size using standard fonts
			'format'  => Pdf::FORMAT_A4,
			'orientation' => Pdf::ORIENT_LANDSCAPE,
			'cssFile' => '@frontend/assets/css/ballot.css',
			'content' => $this->renderPartial("ballots", [
				"model" => $model
			]),
			'options' => [
				'title' => 'Ballots Round #' . $model->number,
			],
		]);

		//renderAjax does the trick for no layout
		return $pdf->render();
	}

	/**
	 * @param integer $aID UserID
	 * @param integer $bID USerID
	 */
	public function actionSwitchAdjudicators($id, $aID, $bID)
	{
		$a = Adjudicator::find()->tournament($this->_tournament->id)->andWhere([
			"user_id" => $aID
		])->one();
		$b = Adjudicator::find()->tournament($this->_tournament->id)->andWhere([
			"user_id" => $bID
		])->one();

		if ($a instanceof Adjudicator && $b instanceof Adjudicator) {
			/** @var AdjudicatorInPanel $a_in_panel */
			$a_in_panel = AdjudicatorInPanel::find()->joinWith("panel")->where([
				"adjudicator_id" => $a->id,
				"panel.used"     => 1,
			])->orderBy(["panel_id" => SORT_DESC])->one();

			$b_in_panel = AdjudicatorInPanel::find()->joinWith("panel")->where([
				"adjudicator_id" => $b->id,
				"panel.used"     => 1,
			])->orderBy(["panel_id" => SORT_DESC])->one();

			$temp = $a_in_panel->panel_id;
			$a_in_panel->panel_id = $b_in_panel->panel_id;
			$b_in_panel->panel_id = $temp;

			if ($a_in_panel->validate() && $b_in_panel->validate()) {
				if ($a_in_panel->save() && $b_in_panel->save())
					Yii::$app->session->addFlash("success", Yii::t("app", "Adjudicator {n1} and {n2} switched", [
						"n1" => $a->getName(),
						"n2" => $b->getName(),
					]));
			} else {
				Yii::$app->session->addFlash("error", Yii::t("app", "Could not switch because: {a_panel}<br>and<br>{b_panel}", [
					"a_panel" => ObjectError::getMsg($a_in_panel),
					"b_panel" => ObjectError::getMsg($b_in_panel),
				]));
				Yii::error("Adjudicator switching error: " . ObjectError::getMsg($a_in_panel) . "\n" . ObjectError::getMsg($b_in_panel), __METHOD__);
			}
		}

		$this->redirect(['view', 'id' => $id, "tournament_id" => $this->_tournament->id]);
	}

	public function actionDebatedetails()
	{
		try {
			$id = Yii::$app->request->post("expandRowKey", 0);
			$debate = Debate::findOne($id);
			if ($debate instanceof Debate)
				return $this->renderAjax("_debate_details", ["model" => $debate]);
		} catch (Exception $ex) {
			return $ex->getMessage();
		}

		return "Error";
	}

	public function actionExport($id, $type = "json")
	{
		$team_props = [
			"id", "speakerA_id", "speakerB_id", "society_id", "isSwing", "points", "speakerA_speaks", "speakerB_speaks",
		];

		$panel_props = [
			"id", "strength", "is_preset"
		];

		$adju_props = [
			"id", "breaking", "strength", "society_id", "can_chair", "are_watched",
		];

		$round = Round::findOne(["id" => $id]);
		if ($round instanceof Round) {

			/** @var Debate $model */
			foreach ($round->debates as $model) {

				$line = [
					"id"           => $model->id,
					"venue"        => $model->venue_id,
					"teams" => [
						"OG" => $model->og_team->toArray($team_props),
						"OO" => $model->oo_team->toArray($team_props),
						"CG" => $model->cg_team->toArray($team_props),
						"CO" => $model->co_team->toArray($team_props),
					],
					"panel"      => $model->panel->toArray($panel_props),
					"messages"	 => [],
					"energy"	 => 0,
				];
				$line["panel"]["adjudicators"] = [];

				foreach ($model->panel->adjudicatorInPanels as $inPanel) {

					/** @var Adjudicator $adju */
					$adju = $inPanel->adjudicator;
					$adjudicator = $adju->toArray($adju_props);
					$adjudicator["societies"] = ArrayHelper::getColumn($adju->getSocieties(true)->asArray()->all(), "id");

					$strikedAdju = $adju->getStrikedAdjudicators()->asArray()->all();
					$adjudicator["strikedAdjudicators"] = $strikedAdju;

					$strikedTeam = $adju->getStrikedTeams()->asArray()->all();
					$adjudicator["strikedTeams"] = $strikedTeam;

					$adjudicator["pastAdjudicatorIDs"] = $adju->getPastAdjudicatorIDs($model->id);
					$adjudicator["pastTeamIDs"] = $adju->getPastTeamIDs($round->id);

					if ($inPanel->function == Panel::FUNCTION_CHAIR) {
						array_unshift($line["panel"]["adjudicators"], $adjudicator);
					} else {
						$line["panel"]["adjudicators"][] = $adjudicator;
					}
				}

				$DRAW[] = $line;
			}
		}

		$output = "";
		$name = str_replace(" ", "-", ($round->tournament->name." ".$round->getName()));

		switch(strtolower($type))
		{
			case "json":
				header("Content-Type: application/json");
				header("Content-Disposition: attachment; filename=".$name.".json");
				header('Pragma: no-cache');
				$output = json_encode($DRAW);
				break;
			case "raw":
				$apptype= "application/text";
				$filetype = "txt";
				$output = print_r($DRAW, true);
				break;
		}

		return $output;
	}

	public function actionImport($id, $type = "json") {

	}
}
