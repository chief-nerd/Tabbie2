<?php

namespace frontend\controllers;

use common\components\filter\UserContextFilter;
use common\models\InSociety;
use Yii;
use common\models\Society;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SocietyController implements the CRUD actions for Society model.
 */
class SocietyController extends BaseUserController {
	public function behaviors() {
		return [
			'userFilter' => [
				'class' => UserContextFilter::className(),
			],
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						'actions' => ['list'],
						'roles' => ['@'],
					],
					[
						'allow' => true,
						'actions' => ['create', 'update'],
						'matchCallback' => function ($rule, $action) {
							return (isset($this->_user) && ($this->_user->id == Yii::$app->user->id || Yii::$app->user->isAdmin()));
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
	 * Creates a new Society model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate() {
		$model = new InSociety();
		$model->user_id = $this->_user->id;

		if ($model->load(Yii::$app->request->post())) {

			$model->society_id = Yii::$app->request->post("InSociety")["society"];

			if ($model->save()) {
				Yii::$app->session->addFlash("success", Yii::t("app", "Society connection created"));
				return $this->redirect(['user/view', 'id' => $this->_user->id]);
			}
			else {
				Yii::$app->session->addFlash("error", Yii::t("app", "Society coud not be saved"));
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
	public function actionUpdate($id) {
		$model = $this->findModel($id);

		if ($model->load(Yii::$app->request->post())) {

			if ($model->save()) {
				Yii::$app->session->addFlash("success", Yii::t("app", "Society Info updated"));
				return $this->redirect(['user/view', 'id' => $this->_user->id]);
			}
			else {
				Yii::$app->session->addFlash("error", Yii::t("app", "Society coud not be saved"));
			}

		}

		return $this->render('update', [
			'model' => $model,
		]);
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

		return $this->redirect(['index']);
	}

	/**
	 * Returns 20 societies in an JSON List
	 *
	 * @param type $search
	 * @param type $id
	 */
	public function actionList($search = null, $id = null) {
		$out = ['more' => false];
		if (!is_null($search)) {
			$query = new \yii\db\Query;
			$query->select(["id", "fullname as text"])
			      ->from('society')
			      ->where('fullname LIKE "%' . $search . '%"')
			      ->limit(20);
			$command = $query->createCommand();
			$data = $command->queryAll();
			$out['results'] = array_values($data);
		}
		elseif ($id > 0) {
			$out['results'] = ['id' => $id, 'text' => Society::findOne($id)->fullname];
		}
		else {
			$out['results'] = ['id' => 0, 'text' => Yii::t("app", 'No matching records found')];
		}
		echo \yii\helpers\Json::encode($out);
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
	protected function findModel($id) {
		if (($model = InSociety::find()->where(["society_id" => $id, "user_id" => $this->_user->id])->one()) !== null) {
			return $model;
		}
		else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
}
