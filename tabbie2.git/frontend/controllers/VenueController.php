<?php

namespace frontend\controllers;

use common\components\filter\TournamentContextFilter;
use common\components\ObjectError;
use common\models\search\VenueSearch;
use common\models\Tournament;
use common\models\Venue;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * VenueController implements the CRUD actions for Venue model.
 *
 * @property Tournament $_tournament
 */
class VenueController extends BaseTournamentController
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
						'actions' => ['index', 'view'],
						'matchCallback' => function ($rule, $action) {
							return ($this->_tournament->isTabMaster(Yii::$app->user->id) || $this->_tournament->isConvenor(Yii::$app->user->id));
						}
					],
					[
						'allow'   => true,
						'actions' => ['create', 'update', 'delete', 'active', 'import'],
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

	/**
	 * Lists all Venue models.
	 *
	 * @return mixed
	 */
	public function actionIndex()
	{
		$search = new VenueSearch(["tournament_id" => $this->_tournament->id]);
		$dataProvider = $search->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single Venue model.
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
	 * Creates a new Venue model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate()
	{
		$model = new Venue();
		$model->tournament_id = $this->_tournament->id;
		$model->active = true;

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['index', 'tournament_id' => $model->tournament_id
			]);
		} else {
			return $this->render('create', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Updates an existing Venue model.
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
			return $this->redirect(['index', 'tournament_id' => $model->tournament_id]);
		} else {
			return $this->render('update', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Deletes an existing Venue model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionDelete($id)
	{
		$this->findModel($id)->delete();

		return $this->redirect(['index', 'tournament_id' => $this->_tournament->id]);
	}

	/**
	 * Toggle a Venue visability
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionActive($id)
	{
		$model = $this->findModel($id);

		if ($model->active == 0)
			$model->active = 1;
		else {
			$model->active = 0;
		}

		if (!$model->save()) {
			Yii::$app->session->addFlash("error", ObjectError::getMsg($model));
		}

		if (Yii::$app->request->isAjax)
			return $this->actionIndex();
		else
			return $this->redirect(['venue/index', 'tournament_id' => $this->_tournament->id]);
	}

	/**
	 * Finds the Venue model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 *
	 * @return Venue the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		if (($model = Venue::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	public function actionImport()
	{
		set_time_limit(0);
		$tournament = $this->_tournament;
		$model = new \frontend\models\ImportForm();

		if (Yii::$app->request->isPost) {
			$model->scenario = "screen";

			if (Yii::$app->request->post("makeItSo", false)) { //Everything corrected
				$model->tempImport = unserialize(Yii::$app->request->post("csvFile", false));
//INSERT DATA
				for ($r = 1; $r <= count($model->tempImport); $r++) {
					$row = $model->tempImport[$r];

					if (Venue::find()
							->tournament($this->_tournament->id)
							->andWhere(['name' => $row[0]])
							->count() == 0
					) {
						$venue = new Venue();
						$venue->name = $row[0];
						$venue->active = $row[1];
						$venue->group = $row[2];
						$venue->tournament_id = $this->_tournament->id;
						$venue->save();
					}
				}

				return $this->redirect(['index', "tournament_id" => $this->_tournament->id]);
			} else { //FORM UPLOAD
				$file = \yii\web\UploadedFile::getInstance($model, 'csvFile');
				$model->load(Yii::$app->request->post());

				$row = 0;
				if ($file && ($handle = fopen($file->tempName, "r")) !== false) {
					while (($data = fgetcsv($handle, 1000, ";")) !== false) {

						if ($row == 0) { //Don't use first row
							$row++;
							continue;
						}

						if (($num = count($data)) != 3) {
							throw new \yii\base\Exception("500", Yii::t("app", "File Syntax Wrong"));
						}

						for ($c = 0; $c < $num; $c++) {
							$model->tempImport[$row][$c] = trim($data[$c]);
						}
						$row++;
					}
					fclose($handle);
				} else {
					Yii::$app->session->addFlash("error", Yii::t("app", "No File available"));
					print_r($file);
				}
			}
		} else
			$model->scenario = "upload";

		return $this->render('import', [
			"model" => $model,
			"tournament" => $tournament
		]);
	}

}
