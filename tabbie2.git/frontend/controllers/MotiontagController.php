<?php

namespace frontend\controllers;

use common\models\LegacyMotion;
use common\models\Motion;
use Yii;
use common\models\MotionTag;
use common\models\search\MotionSearch;
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
						'allow'   => true,
						'actions' => ['index', 'view', 'list'],
						'roles'   => ['?', '@'],
					],
					[
						'allow'   => true,
						'actions' => ['add-motion'],
						'roles'   => ['@'],
					],
					[
						'allow'         => true,
						'actions'       => ['update', 'delete', 'merge'],
						'matchCallback' => function ($rule, $action) {
							return (Yii::$app->user->isAdmin());
						}
					],
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
		$motions = Motion::findAllByTags(false, 100);

		$cloud = MotionTag::find()
			->select(["motion_tag.*", "count(id) as count"])
			->innerJoin("tag", "motion_tag_id = motion_tag.id")
			->groupBy("motion_tag_id")
			->orderBy(["count" => SORT_DESC])
			->having("count > 0")
			->union(
				MotionTag::find()->select(["motion_tag.*", "count(id) as count"])
					->innerJoin("legacy_tag", "motion_tag_id = motion_tag.id")
					->groupBy("motion_tag_id")
					->orderBy(["count" => SORT_DESC])
					->having("count > 0")
			)
			->limit(30);

		$cloud = $cloud->all();

		$heighest = $cloud[0]->count;
		$lowest = $cloud[count($cloud) - 1]->count;

		$span = $heighest - $lowest;

		for ($i = 0; $i < count($cloud); $i++) {
			/** @var Motion $cloud [$i] */
			$cloud[$i] = $cloud[$i]->toArray();
			$count = $cloud[$i]["count"];
			$percent = floor((($count - $lowest) / $span)) * 100;

			$cloud[$i]["size"] = "s" . intval($percent / 20);

		}

		//shuffle($cloud);
		usort($cloud, function ($a, $b) {
			return strcasecmp($a["name"], $b["name"]);
		});

		$dataProvider = new ArrayDataProvider([
			'allModels'  => $motions,
			'pagination' => [
				'pageSize' => Yii::$app->params["motions_per_page"],
			],
		]);

		return $this->render('index', [
			'dataProvider' => $dataProvider,
			'cloud' => $cloud,
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
			'pagination' => [
				'pageSize' => Yii::$app->params["motions_per_page"],
			]
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

	/**
	 * Creates a new LegacyMotion model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionAddMotion()
	{
		$model = new LegacyMotion();
		$model->by_user_id = Yii::$app->user->id;
		$model->language = 'en';

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			$model->saveTags();
			Yii::$app->session->addFlash("success", Yii::t("app", "Thank you for your submission."));

			return $this->redirect(['index']);
		} else {
			return $this->render('add-motion', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Returns 20 Adjudicators in an JSON List
	 *
	 * @param type $search
	 * @param type $id
	 */
	public function actionList(array $search = null, $id = null)
	{
		$out = ['more' => false];
		if (!is_null($search["term"]) && $search["term"] != "") {
			$query = new \yii\db\Query;
			$query->select(["id", "name as text"])
				->from('motion_tag')
				->where('name LIKE "%' . $search["term"] . '%" OR abr LIKE "%' . $search["term"] . '%"')
				->limit(20);
			$command = $query->createCommand();
			$data = $command->queryAll();
			$out['results'] = array_values($data);
		} elseif ($id > 0) {
			$out['results'] = ['id' => $id, 'text' => MotionTag::findOne($id)->name];
		} else {
			$out['results'] = ['id' => 0, 'text' => Yii::t("app", 'No matching records found')];
		}
		echo \yii\helpers\Json::encode($out);
	}
}
