<?php

namespace frontend\controllers;

use common\components\filter\TournamentContextFilter;
use common\models\search\DebateSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use common\models\Adjudicator;
use common\models\Team;

/**
 * TournamentController implements the CRUD actions for Tournament model.
 */
class DisplayController extends BaseTournamentController {

	//Override Layout
	public $layout = "public";
	//MenuRight Items
	public $menuItems = [];

	public function behaviors() {
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						'actions' => ['index', 'view', 'start', 'missinguser'],
						'matchCallback' => function ($rule, $action) {
							return (Yii::$app->user->isTabMaster($this->_tournament) || Yii::$app->user->isConvenor($this->_tournament));
						}
					],
					[
						'allow' => true,
						'actions' => ['markmissing'],
						'matchCallback' => function ($rule, $action) {
							return (Yii::$app->user->isTabMaster($this->_tournament));
						}
					],
				],
			],
			'tournamentFilter' => [
				'class' => TournamentContextFilter::className(),
			],
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'delete' => ['post'],
				],
			],
		];
	}

	public function actionIndex() {

		$rounds = \common\models\Round::find()->where([
			"tournament_id" => $this->_tournament->id,
			"published" => 1,
			"displayed" => 0
		])->all();
		$buttons = "";
		foreach ($rounds as $round) {
			$buttons .= \yii\helpers\Html::a("Show Round " . $round->number, [
				"display/view",
				"id" => $round->id,
				"tournament_id" => $round->tournament_id], ["class" => "btn btn-lg btn-success"]);
		}

		if (Yii::$app->getRequest()->isAjax) {
			return $buttons;
		}
		else {

			$publishpath = Yii::$app->assetManager->publish(Yii::getAlias("@frontend/assets/js/display_roundPoll.js"));
			$this->view->registerJsFile($publishpath[1], [
				"depends" => [
					\yii\web\JqueryAsset::className(),
				],
			]);

			return $this->render("index", [
				"tournament" => $this->_tournament,
				"already" => $buttons
			]);
		}
	}

	public function actionView($id) {
		$round = \common\models\Round::findOne($id);
		$searchModel = new DebateSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->_tournament->id, $id);

		$publishpath = Yii::$app->assetManager->publish(Yii::getAlias("@frontend/assets/js/display_autoScroll.js"));
		$this->view->registerJsFile($publishpath[1], [
			"depends" => [
				\yii\web\JqueryAsset::className(),
			],
		]);

		return $this->render("view", [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
			'round' => $round,
		]);
	}

	public function actionStart($id) {
		$round = \common\models\Round::findOne($id);
		if ($round instanceof \common\models\Round) {
			$round->displayed = 1;
			$round->prep_started = date("Y-m-d H:i:s");
			if ($round->save())
				return "1";
		}
		return "0";
	}

	/**
	 * Show checkin form.
	 *
	 * @return mixed
	 */
	public function actionMissinguser() {

		$teams = Team::find()
		             ->tournament($this->_tournament->id)
		             ->andWhere("speakerA_checkedin = 0 OR speakerB_checkedin = 0")
		             ->all();
		$adjudicators = Adjudicator::find()->tournament($this->_tournament->id)->andWhere(["checkedin" => 0])->all();

		return $this->render('missing', [
			"teams" => $teams,
			"adjudicators" => $adjudicators,
		]);
	}

	/**
	 * Show checkin form.
	 *
	 * @return mixed
	 */
	public function actionMarkmissing() {

		$team = Team::updateAll(["active" => 0],
			"tournament_id = :tid AND ( speakerA_checkedin = 0 OR speakerB_checkedin = 0 )",
			[":tid" => $this->_tournament->id]
		);
		$adju = Adjudicator::updateAll(["active" => 0], ["tournament_id" => $this->_tournament->id, "checkedin" => 0]);

		Yii::$app->session->addFlash("info", $team . " Teams and " . $adju . " Adjudicators set as inactive");

		return $this->redirect(["tournament/view", "id" => $this->_tournament->id]);
	}

}
