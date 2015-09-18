<?php

namespace backend\controllers;

use Yii;
use common\models\Message;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MessageController implements the CRUD actions for Message model.
 */
class MessageController extends Controller {

	public function behaviors() {
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
			'verbs'  => [
				'class'   => VerbFilter::className(),
				'actions' => [
					'delete' => ['post'],
				],
			],
		];
	}

	/**
	 * Lists all Message models.
	 * @return mixed
	 */
	public function actionIndex() {
		$dataProvider = new ActiveDataProvider([
			'query' => Message::find(),
		]);

		return $this->render('index', [
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single Message model.
	 *
	 * @param integer $id
	 * @param string  $language
	 *
	 * @return mixed
	 */
	public function actionView($id, $language) {
		return $this->render('view', [
			'model' => $this->findModel($id, $language),
		]);
	}

	/**
	 * Finds the Message model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @param string  $language
	 *
	 * @return Message the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id, $language) {
		if (($model = Message::findOne(['id' => $id, 'language' => $language])) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	/**
	 * Updates an existing Message model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @param string  $language
	 *
	 * @return mixed
	 */
	public function actionUpdate($id, $language) {
		$model = $this->findModel($id, $language);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id, 'language' => $model->language]);
		} else {
			return $this->render('update', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Deletes an existing Message model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 * @param string  $language
	 *
	 * @return mixed
	 */
	public function actionDelete($id, $language) {
		$this->findModel($id, $language)->delete();

		return $this->redirect(['index']);
	}
}
