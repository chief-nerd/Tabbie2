<?php

namespace frontend\controllers;

use common\components\filter\TournamentContextFilter;
use common\components\ObjectError;
use common\models\Adjudicator;
use common\models\Debate;
use common\models\Panel;
use common\models\Round;
use common\models\search\DebateSearch;
use common\models\Outround;
use common\models\Team;
use common\models\Venue;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * RoundController implements the CRUD actions for Round model.
 */
class OutroundController extends RoundController
{

	public function behaviors()
	{
		return [
			'tournamentFilter' => [
				'class' => TournamentContextFilter::className(),
			],
			'access'           => [
				'class' => AccessControl::className(),
				'rules' => [
					array_merge_recursive(parent::behaviors()["access"]["rules"][0], [
						'actions' => [],
					]),
					array_merge_recursive(parent::behaviors()["access"]["rules"][1], [
						'actions' => ['set-debate'],
					]),
				],
			],
		];
	}

	/**
	 * Creates a new Round model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate()
	{
		if (Adjudicator::find()->active()->tournament($this->_tournament->id)->andWhere("breaking > 0")->count() == 0) {
			Yii::$app->session->addFlash("info", Yii::t("app", "Please set breaking adjudicators first - use the star icon in the action column."));

			return $this->redirect(["adjudicator/index", "tournament_id" => $this->_tournament->id]);
		}

		$model = new Outround();
		$model->tournament_id = $this->_tournament->id;
		$model->type = Round::TYP_OUT;
		$model->scenario = "1Step";
		//$model->setNextRound();

		if ($model->load(Yii::$app->request->post())) {

			if (!$model->save()) {
				Yii::$app->session->setFlash("error", ObjectError::getMsg($model));
			}

			return $this->redirect(['outround/set-debate', 'id' => $model->id, "tournament_id" => $model->tournament_id]);
		}

		return $this->render('create', [
			'model'        => $model,
			'amount_rooms' => $model->level
		]);
	}

	public function actionSetDebate($id)
	{
		$model = Outround::findOne($id);
		$model->scenario = "2Step";

		if ($model->load(Yii::$app->request->post())) {

			if (!$model->generateOutround()) {
				Yii::$app->session->setFlash("error", ObjectError::getMsg($model));
			}

			return $this->redirect(['outround/view', 'id' => $model->id, "tournament_id" => $model->tournament_id]);
		}

		if (empty($model->adjudicators)) {
			$adjus = Adjudicator::find()
				->active()
				->tournament($model->tournament_id)
				->andWhere(["breaking" => 1])
				->all();
			$model->adjudicators = $adjus;
		}
		if (empty($model->venues)) {
			$venues = Venue::find()->tournament($model->tournament_id)->active()->limit($model->level)->all();
			$model->venues = $venues;
		}

		if (empty($model->out_debate)) {
			/** @todo Properly set teams */
			/*
			$teams = Team::find()
				->active()
				->select(["*", "(speakerA_speaks + speakerB_speaks) as team_speaks"])
				->tournament($model->tournament_id)
				->orderBy(["points" => SORT_ASC, "team_speaks" => SORT_DESC])
				->limit($model->level * 4)
				->all();

			for ($i = 0; $i < $model->level; $i++) {
				foreach (Team::getPos() as $p) {
					$t = array_shift($teams);
					$model->outDebate[$i][$p . "_team"] = $t->id;
				}
			}*/
		}

		return $this->render('set', [
			'model'        => $model,
			'amount_rooms' => $model->level
		]);
	}

	/**
	 * Creates a new Round model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionUpdate($id)
	{
		$model = $this->findModel($id);

		if ($model->load(Yii::$app->request->post())) {

			if (!$model->save()) {
				Yii::$app->session->setFlash("error", ObjectError::getMsg($model));
			}

			return $this->redirect(['outround/view', 'id' => $model->id, "tournament_id" => $model->tournament_id]);
		}

		return $this->render('update', [
			'model'        => $model,
			'amount_rooms' => $model->level
		]);
	}
}
