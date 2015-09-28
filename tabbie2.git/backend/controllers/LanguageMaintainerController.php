<?php

namespace backend\controllers;

use Yii;
use backend\models\LanguageMaintainer;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LanguageMaintainerController implements the CRUD actions for LanguageMaintainer model.
 */
class LanguageMaintainerController extends Controller {

	public function behaviors() {
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						'matchCallback' => function ($rule, $action) {
							return (Yii::$app->user->isMaintainer());
						}
					],
				],
			],
		];
	}

	/**
	 * Lists all LanguageMaintainer models.
	 * @return mixed
	 */
	public function actionIndex() {
		$dataProvider = new ActiveDataProvider([
			'query' => LanguageMaintainer::find(),
		]);

		return $this->render('index', [
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single LanguageMaintainer model.
	 * @param integer $user_id
	 * @param string $language_language
	 * @return mixed
	 */
	public function actionView($user_id, $language_language) {
		return $this->render('view', [
			'model' => $this->findModel($user_id, $language_language),
		]);
	}

	/**
	 * Finds the LanguageMaintainer model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $user_id
	 * @param string $language_language
	 * @return LanguageMaintainer the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($user_id, $language_language) {
		if (($model = LanguageMaintainer::findOne(['user_id' => $user_id, 'language_language' => $language_language])) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	/**
	 * Creates a new LanguageMaintainer model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate() {
		$model = new LanguageMaintainer();

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'user_id' => $model->user_id, 'language_language' => $model->language_language]);
		} else {
			return $this->render('create', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Updates an existing LanguageMaintainer model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $user_id
	 * @param string $language_language
	 * @return mixed
	 */
	public function actionUpdate($user_id, $language_language) {
		$model = $this->findModel($user_id, $language_language);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'user_id' => $model->user_id, 'language_language' => $model->language_language]);
		} else {
			return $this->render('update', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Deletes an existing LanguageMaintainer model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $user_id
	 * @param string $language_language
	 * @return mixed
	 */
	public function actionDelete($user_id, $language_language) {
		$this->findModel($user_id, $language_language)->delete();

		return $this->redirect(['index']);
	}
}
