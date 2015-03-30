<?php

namespace frontend\controllers;

use common\components\filter\TournamentContextFilter;
use common\models\feedback;
use common\models\search\FeedbackSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * FeedbackController implements the CRUD actions for feedback model.
 */
class FeedbackController extends BaseTournamentController {

	public function behaviors() {
		return [
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

	/**
	 * Lists all feedback models.
	 *
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new FeedbackSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single feedback model.
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
	 * Creates a new feedback model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate($id) {
		$model = new Feedback();
		$model->debate_id = $id;

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id]);
		}
		else {
			return $this->render('create', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Deletes an existing feedback model.
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
	 * Finds the feedback model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 *
	 * @return feedback the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id) {
		if (($model = feedback::findOne($id)) !== null) {
			return $model;
		}
		else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

}
