<?php

namespace frontend\controllers;

use common\components\filter\UserContextFilter;
use common\models\Country;
use common\models\InSociety;
use Yii;
use common\models\Society;
use yii\filters\AccessControl;
use yii\helpers\HtmlPurifier;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SocietyController implements the CRUD actions for Society model.
 */
class SocietyController extends BaseuserController
{
	public function behaviors()
	{
		return [
			'userFilter' => [
				'class' => UserContextFilter::className(),
			],
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						'actions' => ['list', 'list-country', 'add-new-society'],
						'roles' => ['@', '?'], //Everyone
					],
					[
						'allow'   => true,
						'actions' => ['create', 'update'],
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
		$model = new InSociety();
		$model->user_id = $this->_user->id;

		$socid = Yii::$app->request->post("InSociety")["society_id"];
		if (!is_numeric($socid) && $socid != "") {
			$model->load(Yii::$app->request->post());
			Yii::$app->session["InSociety"] = serialize($model);

			return $this->redirect(["society/add-new-society", "user_id" => $this->_user->id]);
		}

		if ($model->load(Yii::$app->request->post())) {
			$this->finishCreate($model, Yii::$app->request->post());
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * @param SignupForm $model
	 *
	 * @return static
	 */
	private function finishCreate($model)
	{
		if ($model) {
			if ($model->save()) {
				Yii::$app->session->addFlash("success", Yii::t("app", "Society connection successfully created"));

				return $this->redirect(['user/view', 'id' => $this->_user->id]);
			} else {
				Yii::$app->session->addFlash("error", Yii::t("app", "Society could not be saved"));
			}
		}
	}

	public function actionAddNewSociety()
	{
		$model = new Society();

		if ($model->load(Yii::$app->request->post())) {
			$form = unserialize(Yii::$app->session["InSociety"]);
			if ($form instanceof InSociety) {
				if ($model->save()) {
					$form->society_id = $model->id;
					$this->finishCreate($form);
				}
			} else
				Yii::$app->session->addFlash("error", Yii::t("app", "Error in wakeup"));
		}

		$model->fullname = (isset($model->fullname)) ? $model->fullname : unserialize(Yii::$app->session["InSociety"])->society_id;

		return $this->render("newSociety", [
			"model" => $model,
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
				Yii::$app->session->addFlash("success", Yii::t("app", "Society Info updated"));

				return $this->redirect(['user/view', 'id' => $this->_user->id]);
			} else {
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
	public function actionDelete($id)
	{
		$this->findModel($id)->delete();

		return $this->redirect(['index']);
	}

	/**
	 * Returns 20 societies in an JSON List
	 *
	 * @param type $search
	 * @param type $sid
	 */
	public function actionList(array $search = null, $sid = null)
	{
		$search["term"] = HtmlPurifier::process($search["term"]);
		$sid = intval($sid);

		$out = ['more' => false];
		$soc = Society::findOne($sid);

		if (!is_null($search["term"]) && $search["term"] != "") {
			$query = new \yii\db\Query;
			$query->select(["id", "CONCAT(fullname,' (',abr,')') as text"])
				->from('society')
				->andWhere(["LIKE", "fullname", $search["term"]])
				->orWhere(["LIKE", "abr", $search["term"]])
				->limit(20);
			$command = $query->createCommand();
			$data = $command->queryAll();
			$out['results'] = array_values($data);
		} elseif ($soc instanceof Society) {
			$out['results'] = ['id' => $sid, 'text' => $soc->fullname];
		} else {
			$out['results'] = ['id' => 0, 'text' => Yii::t("app", 'No matching records found')];
		}
		echo \yii\helpers\Json::encode($out);
	}

	/**
	 * Returns 20 societies in an JSON List
	 *
	 * @param type $search
	 * @param type $id
	 */
	public function actionListCountry(array $search = null, $cid = null)
	{
		$search["term"] = HtmlPurifier::process($search["term"]);
		$cid = intval($cid);

		$out = ['more' => false];
		if (!is_null($search["term"]) && $search["term"] != "") {
			$query = new \yii\db\Query;
			$query->select(["id", "name as text"])
				->from('country')
				->where(["LIKE", "name", $search["term"]])
				->limit(20);
			$command = $query->createCommand();
			$data = $command->queryAll();
			$out['results'] = array_values($data);
		} elseif ($cid > 0) {
			$out['results'] = ['id' => $cid, 'text' => Country::findOne($cid)->name];
		} else {
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
	protected function findModel($id)
	{
		if (($model = InSociety::find()->where(["society_id" => $id, "user_id" => $this->_user->id])->one()) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
}
