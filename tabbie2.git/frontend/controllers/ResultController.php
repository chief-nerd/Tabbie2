<?php

namespace frontend\controllers;

use common\components\filter\TournamentContextFilter;
use common\components\ObjectError;
use common\models\Debate;
use common\models\Result;
use common\models\search\ResultSearch;
use common\models\Team;
use Yii;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * ResultController implements the CRUD actions for Result model.
 */
class ResultController extends BaseTournamentController
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
						'actions' => ['create'],
						'matchCallback' => function ($rule, $action) {
							/** @TODO: Faster call */

							if ($this->_tournament->validateAccessToken(Yii::$app->request->get("accessToken", false)))
								return true;

							$info = $this->_tournament->getLastDebateInfo(Yii::$app->user->id);

							return Yii::$app->user->hasChairedLastRound($info);
						},
					],
					[
						'allow' => true,
						'actions'       => ['round', 'view'],
						'matchCallback' => function ($rule, $action) {
							return ($this->_tournament->isTabMaster(Yii::$app->user->id) || $this->_tournament->isCA(Yii::$app->user->id));
						}
					],
					[
						'allow'         => true,
						'actions'       => ['index', 'create', 'update', 'manual', 'correctcache', 'checked'],
						'matchCallback' => function ($rule, $action) {
							return ($this->_tournament->isTabMaster(Yii::$app->user->id));
						}
					],
				],
			],
		];
	}

	/**
	 * Lists all Result models.
	 *
	 * @return mixed
	 */
	public function actionIndex()
	{

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
	 * Displays a single Result model.
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionView($id)
	{
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
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
	protected function findModel($id)
	{
		if (($model = Result::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	/**
	 * Checks a Result
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionChecked($id)
	{
		$result = $this->findModel($id);
		if ($result instanceof Result) {
			if ($result->checked == 1)
				$result->checked = 0;
			else
				$result->checked = 1;
			$result->save();
		}

		return $this->actionRound($result->debate->round_id, "table");
	}

	/**
	 * Lists all Result models.
	 *
	 * @return mixed
	 */
	public function actionRound($id, $view = "venue")
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

		$round = $dataProvider->getModels()[0]->round;

		switch ($view) {
			case "venue":
				$view = "venueview";
				break;
			case "table":
				$view = "tableview";
				break;
			default:
				throw new Exception(Yii::t("app", "View does not exist"), 404);
		}

		return $this->render($view, [
			'round_id'     => $id,
			'round' => $round,
			'searchModel'  => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Creates a new Result model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate($id)
	{

		$result = Result::find()->where(["debate_id" => $id])->one();

		if (!$result instanceof Result) {
			$model = new Result();
			$model->debate_id = $id;

			if ($model->load(Yii::$app->request->post()) && $model->validate(["debate_id"])) {
				if ($model->confirmed == "true") {

					$model->entered_by_id = Yii::$app->user->id;

					if ($model->save()) {
						$model->updateTeamCache();

						$roundid = Debate::findOne($id)->round_id;
						$place = Result::find()->joinWith("debate")->where([
							"debate.tournament_id" => $this->_tournament->id,
							"round_id"             => $roundid
						])->count();
						$max = Debate::find()
							->tournament($this->_tournament->id)
							->where(["round_id" => $roundid])
							->count();

						return $this->render('thankyou', [
							"model" => $model,
							"place" => $place,
							"max"   => $max,
						]);
					} else {
						Yii::error("Save Results: " . ObjectError::getMsg($model), __METHOD__);
						Yii::$app->session->addFlash("error", Yii::t("app", "Error saving Results.<br>Please request a paper ballot!"));
					}
				} else {
					$model->rankTeams();

					return $this->render('confirm', [
						'model' => $model,
					]);
				}
			}

			return $this->render('create', [
				'model' => $model,
			]);
		} else //Already entered - prevent reload
		{
			return $this->render('thankyou', [
				"model" => $result,
				"place" => 0,
				"max"   => 0,
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
	public function actionUpdate($id)
	{
		$model = $this->findModel($id);

		if ($model->load(Yii::$app->request->post())) {
			if ($model->confirmed == "true") {

				$model->entered_by_id = Yii::$app->user->id;

				if ($model->save()) {
					$model->updateTeamCache();

					Yii::$app->session->addFlash("success", Yii::t("app", "Result updated"));

					return $this->redirect(['result/round', 'id' => $model->debate->round_id, "tournament_id" => $this->_tournament->id]);
				} else {
					Yii::error("Save Results: " . ObjectError::getMsg($model), __METHOD__);
					Yii::$app->session->addFlash("error", Yii::t("app", "Error saving Results.<br>Please request a paper ballot!"));
				}
			} else {
				$model->rankTeams();

				return $this->render('confirm', [
					'model' => $model,
				]);
			}
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing Result model.
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

	public function actionManual()
	{

		$model = new Result();

		if ($model->load(Yii::$app->request->post())) {

			if ($model->debate instanceof Debate) {
				if ($model->confirmed == "true") {
					$model->entered_by_id = Yii::$app->user->id;
					if ($model->save()) {
						$model->updateTeamCache();
						Yii::$app->session->addFlash("success", Yii::t("app", "Result saved. Next one!"));
						/* Reset for next one */
						$model = new Result();
					} else {
						Yii::$app->session->addFlash("error", ObjectError::getMsg($model));
					}
				} else {
					$model->rankTeams();

					return $this->render('confirm', [
						'model' => $model,
					]);
				}
			} else {
				$model->addError("debate_id", Yii::t("app", "Debate #{id} does not exist", ["id" => $model->debate_id]));
			}
		}

		return $this->render('manual', [
			'model' => $model,
		]);
	}

	public function actionCorrectcache()
	{
		$teams = \common\models\Team::find()->tournament($this->_tournament->id)->all();

		$found = false;

		foreach ($teams as $team) {
			/** @var $team Team * */

			$values = $team->getNewCacheData();
			$calculated_points = $values["Points"];
			$calculated_A_speaks = $values[Team::POS_A];
			$calculated_B_speaks = $values[Team::POS_B];

			if ($calculated_points != $team->points) {
				Yii::$app->session->addFlash("info", Yii::t("app", "Correct Points for {team} from {old_points} to {new_points}", [
					"team" => $team->name,
					"old_points" => $team->points,
					"new_points" => $calculated_points,

				]));
				$team->points = $calculated_points;
				$team->save(false);
				$found = true;
			}

			if ($calculated_A_speaks != $team->speakerA_speaks) {
				Yii::$app->session->addFlash("info", Yii::t("app", "Correct SpeakerA speaks for {team} from {old_points} to {new_points}", [
					"team" => $team->name,
					"old_points" => $team->speakerA_speaks,
					"new_points" => $calculated_A_speaks,

				]));
				$team->speakerA_speaks = $calculated_A_speaks;
				$team->save(false);
				$found = true;
			}

			if ($calculated_B_speaks != $team->speakerB_speaks) {
				Yii::$app->session->addFlash("info", Yii::t("app", "Correct SpeakerB Speaks for {team} from {old_points} to {new_points}", [
					"team" => $team->name,
					"old_points" => $team->speakerB_speaks,
					"new_points" => $calculated_B_speaks,

				]));
				$team->speakerB_speaks = $calculated_B_speaks;
				$team->save(false);
				$found = true;
			}
		}

		if ($found == false)
			Yii::$app->session->addFlash("success", Yii::t("app", "Cache in perfect shape. No change needed!"));

		return $this->redirect(['result/index', "tournament_id" => $this->_tournament->id]);
	}

}
