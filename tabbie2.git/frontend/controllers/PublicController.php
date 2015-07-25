<?php

namespace frontend\controllers;

use common\components\filter\TournamentContextFilter;
use common\models\search\DebateSearch;
use common\models\search\ResultSearch;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use common\models\Adjudicator;
use common\models\Team;
use yii\data\ActiveDataProvider;

/**
 * TournamentController implements the CRUD actions for Tournament model.
 */
class PublicController extends BaseTournamentController
{

	//Override Layout
	public $layout = "public";
	//MenuRight Items
	public $menuItems = [];

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
						'actions' => ['rounds', 'draw', 'start-round', 'missing-user', 'runner-view'],
						'matchCallback' => function ($rule, $action) {
							return $this->_tournament->validateAccessToken(Yii::$app->request->get("accessToken", ""));
						}
					],
					[
						'allow'   => true,
						'actions' => ['mark-missing'],
						'matchCallback' => function ($rule, $action) {
							return ($this->_tournament->isTabMaster(Yii::$app->user->id));
						}
					],
				],
			],
			'verbs'  => [
				'class' => VerbFilter::className(),
				'actions' => [
					'delete' => ['post'],
				],
			],
		];
	}

	public function actionRounds()
	{

		$rounds = \common\models\Round::find()->where([
			"tournament_id" => $this->_tournament->id,
			"published" => 1,
			"displayed" => 0
		])->all();
		$buttons = "";
		foreach ($rounds as $round) {
			$buttons .= \yii\helpers\Html::a(Yii::t("app", "Show Round {number}", ["number" => $round->number]), [
				"public/draw",
				"id"          => $round->id,
				"tournament_id" => $round->tournament_id,
				"accessToken" => $round->tournament->accessToken], ["class" => "btn btn-lg btn-success"]);
		}

		if (Yii::$app->getRequest()->isAjax) {
			return $buttons;
		} else {

			$publishpath = Yii::$app->assetManager->publish(Yii::getAlias("@frontend/assets/js/display_roundPoll.js"));
			$this->view->registerJsFile($publishpath[1], [
				"depends" => [
					\yii\web\JqueryAsset::className(),
				],
			]);

			return $this->render("rounds", [
				"tournament" => $this->_tournament,
				"already" => $buttons
			]);
		}
	}

	public function actionDraw($id)
	{
		$round = \common\models\Round::findOne($id);
		$searchModel = new DebateSearch();
		/** @var ActiveDataProvider $dataProvider */
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->_tournament->id, $id);

		$publishpath = Yii::$app->assetManager->publish(Yii::getAlias("@frontend/assets/js/display_autoScroll.js"));
		$this->view->registerJsFile($publishpath[1], [
			"depends" => [
				\yii\web\JqueryAsset::className(),
			],
		]);

		$draw_array = [];
		foreach ($dataProvider->models as $model) {
			foreach (Team::getPos() as $pos) {
				$item = clone $model;
				$item->draw_sort = $model->{$pos . "_team"}->name;
				$draw_array[] = $item;
			}
		}

		$draw = new ArrayDataProvider([
			'allModels' => $draw_array,
			'sort' => [
				'attributes' => ['draw_sort'],
				'defaultOrder' => ['draw_sort' => SORT_ASC]
			]
		]);

		return $this->render("draw", [
			'searchModel' => $searchModel,
			//'dataProvider' => $dataProvider,
			'dataProvider' => $draw,
			'round'       => $round,
		]);
	}

	public function actionStartRound($id)
	{
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
	public function actionMissingUser()
	{

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
	public function actionMarkMissing()
	{

		$team = Team::updateAll(["active" => 0],
			"tournament_id = :tid AND ( (speakerA_checkedin = 0) OR (speakerB_checkedin = 0) )",
			[":tid" => $this->_tournament->id]
		);
		$adju = Adjudicator::updateAll(["active" => 0], ["tournament_id" => $this->_tournament->id, "checkedin" => 0]);

		Yii::$app->session->addFlash("info", $team . " Teams and " . $adju . " Adjudicators set as inactive");

		return $this->redirect(["tournament/view", "id" => $this->_tournament->id]);
	}

	public function actionRunnerView($id)
	{
		$searchModel = new ResultSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->_tournament->id, $id);

		$publishpath = Yii::$app->assetManager->publish(Yii::getAlias("@frontend/assets/js/result_poll.js"));
		$this->view->registerJsFile($publishpath[1], [
			"depends" => [
				\yii\web\JqueryAsset::className(),
				\yii\widgets\PjaxAsset::className(),
			],
		]);

		if ($dataProvider->getCount() == 0) {
			Yii::$app->session->addFlash("info", Yii::t("app", "No debates found in that round"));
			$this->redirect(["result/index", "tournament_id" => $this->_tournament->id]);
		}

		$number = $dataProvider->getModels()[0]->round->number;


		return $this->render("runner", [
			'round_id'    => $id,
			'round_number' => $number,
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

}
