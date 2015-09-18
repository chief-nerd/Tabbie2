<?php

namespace frontend\controllers;

use common\components\filter\UserContextFilter;
use common\components\ObjectError;
use common\models\Country;
use common\models\InSociety;
use common\models\UserClash;
use Yii;
use common\models\Society;
use yii\filters\AccessControl;
use yii\helpers\HtmlPurifier;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SocietyController implements the CRUD actions for Society model.
 */
class ClashController extends BaseuserController
{
	public function behaviors()
	{
		return [
			'userFilter' => [
				'class' => UserContextFilter::className(),
			],
			'access'     => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow'         => true,
						'actions'       => ['create', 'update', 'delete'],
						'matchCallback' => function ($rule, $action) {
							return (isset($this->_user) && ($this->_user->id == Yii::$app->user->id || Yii::$app->user->isAdmin()));
						}
					],
				],
			],
		];
	}


	/**
	 * Creates a new Society model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate()
	{
		$model = new UserClash();
		$model->user_id = $this->_user->id;

		if ($model->load(Yii::$app->request->post())) {
			if ($model->save()) {
				Yii::$app->session->addFlash("success", Yii::t("app", "{object} created", [
					'object' => Yii::t("app", 'Individual clash')
				]));

				return $this->redirect(["user/view", "id" => $this->_user->id]);
			} else {
				Yii::$app->session->addFlash("error", Yii::t("app", "Individual clash could not be saved"));
				Yii::error("Individual Clash Save:\n" . ObjectError::getMsg($model), __METHOD__);
			}
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing Society model.
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

			if ($model->save()) {
				Yii::$app->session->addFlash("success", Yii::t("app", "{object} updated", [
					'object' => Yii::t("app", "Individual clash"),
				]));

				return $this->redirect(['user/view', 'id' => $this->_user->id]);
			} else {
				Yii::$app->session->addFlash("error", Yii::t("app", "{object} could not be saved", [
					'object' => Yii::t("app", "Individual clash"),
				]));
			}
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Finds the Society model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 *
	 * @return Society the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		if (($model = UserClash::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	/**
	 * Deletes an existing Society model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionDelete($id) {
		$this->findModel($id)->delete();

		Yii::$app->session->addFlash("success", Yii::t("app", "{object} deleted", [
			'object' => Yii::t("app", "Individual clash")
		]));

		return $this->redirect(['user/view', 'id' => $this->_user->id]);
	}
}
