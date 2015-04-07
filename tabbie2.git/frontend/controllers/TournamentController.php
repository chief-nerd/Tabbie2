<?php

namespace frontend\controllers;

use common\components\filter\TournamentContextFilter;
use common\models;
use common\models\search\TournamentSearch;
use common\models\Tournament;
use frontend\models\CheckinForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * TournamentController implements the CRUD actions for Tournament model.
 */
class TournamentController extends BaseTournamentController {

	public function behaviors() {
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						'actions' => ['index', 'view', 'checkin'],
						'roles' => [],
					],
					[
						'allow' => true,
						'actions' => ['create'],
						'roles' => ['@'],
					],
					[
						'allow' => true,
						'actions' => ['update', 'checkinreset', 'missinguser'],
						'matchCallback' => function ($rule, $action) {
							return (Yii::$app->user->isTabMaster($this->_tournament) || Yii::$app->user->isConvenor($this->_tournament));
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

	/**
	 * Lists all Tournament models.
	 *
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new TournamentSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a current Tournament model.
	 *
	 * @return mixed
	 */
	public function actionView($id) {
		return $this->render('view', ['model' => $this->findModel($id),]);
	}

	/**
	 * Creates a new Tournament model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate() {
		$model = new Tournament();
		$model->status = Tournament::STATUS_RUNNING;

		if (Yii::$app->request->isPost) {
			$file = UploadedFile::getInstance($model, 'logo');
			$model->load(Yii::$app->request->post());
			$model->generateUrlSlug();
			if ($file instanceof UploadedFile) {
				$model->saveLogo($file);
			}
			else
				$model->logo = null;

			if ($model->save()) {
				$energyConf = new models\EnergyConfig();
				if ($energyConf->setup($model))
					Yii::$app->session->addFlash("success", "Tournament successfully created");
				else
					Yii::$app->session->addFlash("warning", "Tournament created but Energy config failed!" . print_r($energyConf->getErrors(), true));

				return $this->redirect(['view', 'id' => $model->id]);
			}
			else {
				Yii::$app->session->setFlash("error", "Can't save Tournament!" . print_r($model->getErrors(), true));
			}
		}
		return $this->render('create', ['model' => $model,]);
	}

	/**
	 * Updates an existing Tournament model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionUpdate($id) {
		$model = $this->findModel($id);

		if (Yii::$app->request->isPost) {

			//Upload File
			$file = \yii\web\UploadedFile::getInstance($model, 'logo');

			//Save Old File Path
			$oldFile = $model->logo;
			//Load new values
			$model->load(Yii::$app->request->post());

			if ($file instanceof UploadedFile) {
				//Save new File
				$model->saveLogo($file);
			}
			else
				$model->logo = $oldFile;

			if ($model->save()) {
				Yii::$app->cache->set("tournament" . $model->id, $model, 120);
				return $this->redirect(['view', 'id' => $model->id]);
			}
		}

		return $this->render('update', ['model' => $model,]);
	}

	/**
	 * Deletes an existing Tournament model.
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
	 * Finds the Tournament model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 *
	 * @return Tournament the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id) {
		if (($model = Tournament::findByPk($id)) !== null) {
			return $model;
		}
		else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	/**
	 * @param Tournament $tournament
	 *
	 * @return int|false
	 */
	public function activeInputAvailable($tournament) {
		$user_id = Yii::$app->user->id;

		$activeRound = models\Round::findOne(["tournament_id" => $tournament->id, "displayed" => 1, "published" => 1, "closed" => 0,]);

		if ($activeRound) {
			$debate = models\Debate::findOneByChair($user_id, $tournament->id, $activeRound->id);
			if ($debate instanceof models\Debate) return $debate->id;
		}

		return false;
	}

	/**
	 * Show checkin form.
	 *
	 * @return mixed
	 */
	public function actionCheckin() {

		$messages = [];
		$model = new CheckinForm();

		if (Yii::$app->request->isPost) {

			$model->load(Yii::$app->request->post());

			$messages = $model->save();
			$model->number = null;
		}


		return $this->render('checkin', [
			"model" => $model,
			"messages" => $messages
		]);
	}

	/**
	 * Show checkin form.
	 *
	 * @return mixed
	 */
	public function actionCheckinreset() {

		$rows = models\Team::updateAll(["speakerA_checkedin" => 0, "speakerB_checkedin" => 0], ["tournament_id" => $this->_tournament->id]);
		$rows += models\Adjudicator::updateAll(["checkedin" => 0], ["tournament_id" => $this->_tournament->id]);

		if ($rows > 0)
			Yii::$app->session->addFlash("success", "Checking Data reseted");
		else
			Yii::$app->session->addFlash("info", "Already clean");

		return $this->redirect(["tournament/view", "id" => $this->_tournament->id]);
	}

}
