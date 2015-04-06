<?php

namespace frontend\controllers;

use common\components\filter\TournamentContextFilter;
use common\models\Debate;
use common\models\Result;
use common\models\search\ResultSearch;
use Yii;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * ResultController implements the CRUD actions for Result model.
 */
class ResultController extends BaseTournamentController {

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
						'actions' => ['index', 'round', 'view', 'create', 'update', 'manual', 'correctcache'],
						'matchCallback' => function ($rule, $action) {
							return (Yii::$app->user->isTabMaster($this->_tournament));
						}
					],
				],
			],
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'delete' => ['post'],
				],
			],
		];
	}

	/**
	 * Lists all Result models.
	 *
	 * @return mixed
	 */
	public function actionIndex() {

		$rounds = new ActiveDataProvider([
			'query' => \common\models\Round::findBySql("SELECT round.* FROM round "
				. "LEFT JOIN debate ON round.id = debate.round_id "
				. "LEFT JOIN result ON result.debate_id = debate.id "
				. "WHERE round.tournament_id = " . $this->_tournament->id . " "
				. "GROUP BY round.id")
		]);

		return $this->render('index', [
			'rounds' => $rounds,
		]);
	}

	/**
	 * Lists all Result models.
	 *
	 * @return mixed
	 */
	public function actionRound($id, $view = "venue") {
		$searchModel = new ResultSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->_tournament->id, $id);

		$publishpath = Yii::$app->assetManager->publish(Yii::getAlias("@frontend/assets/js/result_poll.js"));
		$this->view->registerJsFile($publishpath[1], [
			"depends" => [
				\yii\web\JqueryAsset::className(),
				\yii\widgets\PjaxAsset::className(),
			],
		]);

		if ($dataProvider->getCount() == 0)
			throw new Exception("No Debates found");

		$number = $dataProvider->getModels()[0]->round->number;

		switch ($view) {
			case "venue":
				$view = "venueview";
				break;
			case "table":
				$view = "tableview";
				break;
			default:
				throw new Exception("View does not exist", 404);
		}

		return $this->render($view, [
			'round_id' => $id,
			'round_number' => $number,
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single Result model.
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionView($id) {
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	/**
	 * Creates a new Result model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate($id) {

		$result = Result::find()->where(["debate_id" => $id])->one();

		if (!$result instanceof Result) {
			$model = new Result();
			$model->debate_id = $id;

			if ($model->load(Yii::$app->request->post())) {
				if ($model->confirmed == "true") {

					$model->entered_by_id = Yii::$app->user->id;
					if ($model->save()) {
						$model->updateTeamCache();

						$roundid = Debate::findOne($id)->round_id;
						$place = Result::find()->joinWith("debate")->where([
							"debate.tournament_id" => $this->_tournament->id,
							"round_id" => $roundid
						])->count();
						$max = Debate::find()
						             ->tournament($this->_tournament->id)
						             ->where(["round_id" => $roundid])
						             ->count();

						return $this->render('thankyou', [
							"model" => $model,
							"place" => $place,
							"max" => $max,
						]);
					}
					else {
						Yii::error("Save Results: " . print_r($model->getErrors(), true), __METHOD__);
						Yii::$app->session->addFlash("error", "Error saving Results.<br>Please request a paper ballot!");
					}
				}
				else {
					$model->rankTeams();
					return $this->render('confirm', [
						'model' => $model,
					]);
				}
			}

			return $this->render('create', [
				'model' => $model,
			]);
		}
		else //Already entered - prevent reload
		{
			return $this->render('thankyou', [
				"model" => $result,
				"place" => 0,
				"max" => 0,
			]);
		}
	}

	/**
	 * Updates an existing Result model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionUpdate($id) {
		$model = $this->findModel($id);

		if ($model->load(Yii::$app->request->post())) {
			if ($model->confirmed == "true") {

				$model->entered_by_id = Yii::$app->user->id;
				if ($model->save()) {
					$model->updateTeamCache();

					Yii::$app->session->addFlash("success", "Result updated");
					return $this->redirect(['result/round', 'id' => $model->debate->round_id, "tournament_id" => $this->_tournament->id]);
				}
				else {
					Yii::error("Save Results: " . print_r($model->getErrors(), true), __METHOD__);
					Yii::$app->session->addFlash("error", "Error saving Results.<br>Please request a paper ballot!");
				}
			}
			else {
				$model->rankTeams();
				return $this->render('confirm', [
					'model' => $model,
				]);
			}
		}
		else {
			return $this->render('update', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Deletes an existing Result model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionDelete($id) {
		$this->findModel($id)->delete();

		return $this->redirect(['index']);
	}

	/**
	 * Finds the Result model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 *
	 * @return Result the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id) {
		if (($model = Result::findOne($id)) !== null) {
			return $model;
		}
		else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	public function actionManual() {

		$model = new Result();

		if ($model->load(Yii::$app->request->post())) {
			if ($model->confirmed == "true") {
				$adj = \common\models\Adjudicator::findOne(["user_id" => Yii::$app->user->id]);
				$model->enteredBy_adjudicator_id = $adj->id;
				if ($model->save())
					return $this->render('thankyou', ["model" => $model]);
				else {
					print_r($model->getErrors());
				}
			}
			else {
				$model->rankTeams();
				return $this->render('confirm', [
					'model' => $model,
				]);
			}
		}

		return $this->render('manual', [
			'model' => $model,
		]);
	}

	public function actionCorrectcache() {
		$results = \common\models\Result::find()->leftJoin("debate", "debate.id = result.debate_id")->where([
			"debate.tournament_id" => $this->_tournament->id,
		])->all();

		$teams = \common\models\Team::find()->tournament($this->_tournament->id)->all();

		$found = false;

		foreach ($teams as $team) {
			$calculated_points = 0;
			$calculated_A_speaks = 0;
			$calculated_B_speaks = 0;
			foreach (Debate::positions() as $pos) {

				$results = \common\models\Result::find()
				                                ->leftJoin("debate", "debate.id = result.debate_id")
				                                ->where([
					                                "debate.tournament_id" => $this->_tournament->id,
					                                "debate." . $pos . "_team_id" => $team->id,
				                                ])->all();
				if (is_array($results)) {
					foreach ($results as $res) {
						/**
						 * @var Result $res
						 */
						$calculated_points += (4 - $res->{$pos . "_place"});
						$calculated_A_speaks += $res->{$pos . "_A_speaks"};
						$calculated_B_speaks += $res->{$pos . "_B_speaks"};
					}
				}
			}

			if ($calculated_points != $team->points) {
				Yii::$app->session->addFlash("info", "Correct Points for " . $team->name . " from " . $team->points . " to " . $calculated_points);
				$team->points = $calculated_points;
				$team->save();
				$found = true;
			}

			if ($calculated_A_speaks != $team->speakerA_speaks) {
				Yii::$app->session->addFlash("info", "Correct SpeakerA Speaks for " . $team->name . " from " . $team->speakerA_speaks . " to " . $calculated_A_speaks);
				$team->speakerA_speaks = $calculated_A_speaks;
				$team->save();
				$found = true;
			}

			if ($calculated_B_speaks != $team->speakerB_speaks) {
				Yii::$app->session->addFlash("info", "Correct SpeakerB Speaks for " . $team->name . " from " . $team->speakerB_speaks . " to " . $calculated_B_speaks);
				$team->speakerB_speaks = $calculated_B_speaks;
				$team->save();
				$found = true;
			}
		}

		if ($found == false)
			Yii::$app->session->addFlash("success", Yii::t("app", "Cache in perfect shape. No change needed!"));

		return $this->redirect(['result/index', "tournament_id" => $this->_tournament->id]);
	}

}
