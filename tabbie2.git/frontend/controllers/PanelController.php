<?php

namespace frontend\controllers;

use common\models\Adjudicator;
use common\models\AdjudicatorInPanel;
use Yii;
use common\models\Panel;
use common\models\search\PanelSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\components\filter\TournamentContextFilter;
use yii\filters\AccessControl;

/**
 * PanelController implements the CRUD actions for Panel model.
 */
class PanelController extends BaseTournamentController {
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
						'actions' => ['index', 'view', 'printballots', 'debatedetails'],
						'matchCallback' => function ($rule, $action) {
							return (Yii::$app->user->isTabMaster($this->_tournament) || Yii::$app->user->isConvenor($this->_tournament));
						}
					],
					[
						'allow' => true,
						'actions' => ['create', 'update', 'changevenue', 'publish', 'redraw', 'improve'],
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
	 * Lists all Panel models.
	 *
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new PanelSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->_tournament->id);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single Panel model.
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
	 * Creates a new Panel model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate() {
		$model = new Panel();
		$model->tournament_id = $this->_tournament->id;
		$model->used = 0;
		$model->is_preset = 1;

		if ($model->load(Yii::$app->request->post())) {
			if ($model->createAIP()) {
				return $this->redirect(['view', 'id' => $model->id, "tournament_id" => $this->_tournament->id]);
			}
			else {
				Yii::$app->session->addFlash("error", Yii::t("app", "Error saving:") . print_r($model->getErrors(), true));
				for ($i = 0; $i < 4; $i++)
					$model->set_adjudicators[] = Yii::$app->request->post("Panel")["set_adjudicators"][$i];
			}
		}
		else {
			for ($i = 0; $i < 4; $i++)
				$model->set_adjudicators[] = new Adjudicator();
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing Panel model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionUpdate($id) {
		$model = $this->findModel($id);
		$model->tournament_id = $this->_tournament->id;

		if ($model->load(Yii::$app->request->post()) && $model->save()) {

			if ($model->createAIP())
				return $this->redirect(['view', 'id' => $model->id, "tournament_id" => $this->_tournament->id]);
		}

		foreach ($model->getAdjudicatorInPanels()->orderBy(['function' => SORT_DESC])->all() as $aip) {
			$model->set_adjudicators[] = $aip->adjudicator;
		}
		for ($i = 0; $i < 2; $i++)
			$model->set_adjudicators[] = new Adjudicator();

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing Panel model.
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
	 * Finds the Panel model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 *
	 * @return Panel the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id) {
		if (($model = Panel::findOne($id)) !== null) {
			return $model;
		}
		else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
}
