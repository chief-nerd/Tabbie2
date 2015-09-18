<?php

namespace backend\controllers;

use common\models\LegacyTag;
use common\models\Motion;
use common\models\Tag;
use Yii;
use common\models\MotionTag;
use common\models\search\MotionTagSearch;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MotiontagController implements the CRUD actions for MotionTag model.
 */
class MotiontagController extends Controller
{
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow'         => true,
						'matchCallback' => function ($rule, $action) {
							return (Yii::$app->user->isMaintainer());
						}
					],
				],
			],
			'verbs' => [
				'class'   => VerbFilter::className(),
				'actions' => [
					'delete' => ['post'],
				],
			],
		];
	}

	/**
	 * Lists all MotionTag models.
	 * @return mixed
	 */
	public function actionIndex()
	{
		$searchModel = new MotionTagSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel'  => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single MotionTag model.
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionView($id)
	{
		$motions = Motion::findAllByTags([$id]);

		// add conditions that should always apply here

		$dataProvider = new ArrayDataProvider([
			'allModels' => $motions,
		]);

		return $this->render('view', [
			'model'        => $this->findModel($id),
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Finds the MotionTag model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 *
	 * @return MotionTag the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		if (($model = MotionTag::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	/**
	 * Creates a new MotionTag model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate()
	{
		$model = new MotionTag();

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id]);
		} else {
			return $this->render('create', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Updates an existing MotionTag model.
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
			return $this->redirect(['view', 'id' => $model->id]);
		} else {
			return $this->render('update', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Updates an existing MotionTag model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionMerge($id, $other)
	{
		$count = Tag::updateAll(["motion_tag_id" => $other], ["motion_tag_id" => $id]);
		$count += LegacyTag::updateAll(["motion_tag_id" => $other], ["motion_tag_id" => $id]);

		$this->findModel($id)->delete();

		Yii::$app->session->addFlash("notice", Yii::t("app", "{count} Tags switched", ["count" => $count]));

		return $this->redirect(['index']);
	}

	/**
	 * Deletes an existing MotionTag model.
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
}
