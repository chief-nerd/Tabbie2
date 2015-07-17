<?php

namespace frontend\controllers;

use common\models\AdjudicatorStrike;
use common\models\TeamStrike;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use common\components\filter\TournamentContextFilter;
use yii\filters\AccessControl;

/**
 * StrikesController implements the CRUD actions for Strikes model.
 */
class StrikeController extends BaseTournamentController {
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
						'actions' => ['team_index', 'team_create', 'team_update', 'team_delete', 'adjudicator_index', 'adjudicator_create', 'adjudicator_update', 'adjudicator_delete'],
						'matchCallback' => function ($rule, $action) {
							return ($this->_tournament->isTabMaster(Yii::$app->user->id));
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
	 * Lists all Strikes models.
	 *
	 * @return mixed
	 */
	public function actionTeam_index() {
		$dataProvider = new ActiveDataProvider([
			'query' => TeamStrike::find()->where(["tournament_id" => $this->_tournament->id])
		]);

		return $this->render('team_index', [
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Lists all Strikes models.
	 *
	 * @return mixed
	 */
	public function actionAdjudicator_index() {
		$dataProvider = new ActiveDataProvider([
			'query' => AdjudicatorStrike::find()->where(["tournament_id" => $this->_tournament->id])
		]);

		return $this->render('adjudicator_index', [
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single Strikes model.
	 *
	 * @param integer $team_id
	 * @param integer $adjudicator_id
	 *
	 * @return mixed
	 */
	public function actionView($team_id, $adjudicator_id) {
		return $this->render('view', [
			'model' => $this->findModel($team_id, $adjudicator_id),
		]);
	}

	/**
	 * Creates a new Team Strikes model.
	 * If creation is successful, the browser will be redirected to the 'index' page.

	 *
*@return mixed
	 */
	public function actionTeam_create() {
		$model = new TeamStrike();
		$model->tournament_id = $this->_tournament->id;

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['team_index', "tournament_id" => $this->_tournament->id]);
		}
		else {
			return $this->render('team_create', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Creates a new Adjudicator Strikes model.
	 * If creation is successful, the browser will be redirected to the 'index' page.
	 *
	 * @return mixed
	 */
	public function actionAdjudicator_create() {
		$model = new AdjudicatorStrike();
		$model->tournament_id = $this->_tournament->id;

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['adjudicator_index', "tournament_id" => $this->_tournament->id]);
		}
		else {
			return $this->render('adjudicator_create', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Updates an existing Strikes model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $team_id
	 * @param integer $adjudicator_id
	 *
	 * @return mixed
	 */
	public function actionTeam_update($team_id, $adjudicator_id) {
		$model = $this->findTeamModel($team_id, $adjudicator_id);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['team_index', 'tournament_id' => $model->tournament_id]);
		}
		else {
			return $this->render('team_update', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Updates an existing Strikes model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $team_id
	 * @param integer $adjudicator_id
	 *
	 * @return mixed
	 */
	public function actionAdjudicator_update($adjudicator_from_id, $adjudicator_to_id) {
		$model = $this->findAdjudicatorModel($adjudicator_from_id, $adjudicator_to_id);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['adjudicator_index', 'tournament_id' => $model->tournament_id]);
		}
		else {
			return $this->render('adjudicator_update', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Deletes an existing Strikes model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $team_id
	 * @param integer $adjudicator_id
	 *
	 * @return mixed
	 */
	public function actionTeam_delete($team_id, $adjudicator_id) {
		$this->findTeamModel($team_id, $adjudicator_id)->delete();

		return $this->redirect(['team_index', "tournament_id" => $this->_tournament->id]);
	}

	/**
	 * Deletes an existing Strikes model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $adjudicator_from_id
	 * @param integer $adjudicator_to_id
	 *
	 * @return mixed
	 */
	public function actionAdjudicator_delete($adjudicator_from_id, $adjudicator_to_id) {
		$this->findAdjudicatorModel($adjudicator_from_id, $adjudicator_to_id)->delete();

		return $this->redirect(['adjudicator_index', "tournament_id" => $this->_tournament->id]);
	}

	/**
	 * Finds the Strikes model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $team_id
	 * @param integer $adjudicator_id
	 *
	 * @return Strikes the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findTeamModel($team_id, $adjudicator_id) {
		if (($model = TeamStrike::findOne(['team_id' => $team_id, 'adjudicator_id' => $adjudicator_id])) !== null) {
			return $model;
		}
		else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	/**
	 * Finds the Strikes model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $adjudicator_from_id
	 * @param integer $adjudicator_to_id
	 *
	 * @return Strikes the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findAdjudicatorModel($adjudicator_from_id, $adjudicator_to_id) {
		if (($model = AdjudicatorStrike::findOne(['adjudicator_from_id' => $adjudicator_from_id, 'adjudicator_to_id' => $adjudicator_to_id])) !== null) {
			return $model;
		}
		else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
}
