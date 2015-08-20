<?php

namespace frontend\controllers;

use common\components\ObjectError;
use common\models\Adjudicator;
use common\models\AdjudicatorStrike;
use common\models\Team;
use common\models\TeamStrike;
use common\models\User;
use common\models\UserClash;
use Yii;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use common\components\filter\TournamentContextFilter;
use yii\filters\AccessControl;

/**
 * StrikesController implements the CRUD actions for Strikes model.
 */
class StrikeController extends BasetournamentController
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
						'actions' => ['team_index', 'team_create', 'team_update', 'team_delete', 'adjudicator_index', 'adjudicator_create', 'adjudicator_update', 'adjudicator_delete'],
						'matchCallback' => function ($rule, $action) {
							return ($this->_tournament->isTabMaster(Yii::$app->user->id) || $this->_tournament->isCA(Yii::$app->user->id));
						}
					],
					[
						'allow'         => true,
						'actions'       => ['import'],
						'matchCallback' => function ($rule, $action) {
							return ($this->_tournament->isTabMaster(Yii::$app->user->id) && Yii::$app->user->model->role >= User::ROLE_TABMASTER);
						}
					],
				],
			],
		];
	}

	/**
	 * Lists all Strikes models.
	 *
	 * @return mixed
	 */
	public function actionTeam_index()
	{
		$dataProvider = new ActiveDataProvider([
			'query' => TeamStrike::find()->where(["tournament_id" => $this->_tournament->id])
		]);

		return $this->render('team_index', [
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Lists all Strikes models.
	 *
	 * @return mixed
	 */
	public function actionAdjudicator_index()
	{
		$dataProvider = new ActiveDataProvider([
			'query' => AdjudicatorStrike::find()->where(["tournament_id" => $this->_tournament->id])
		]);

		return $this->render('adjudicator_index', [
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single Strikes model.
	 *
	 * @param integer $team_id
	 * @param integer $adjudicator_id
	 *
	 * @return mixed
	 */
	public function actionView($team_id, $adjudicator_id)
	{
		return $this->render('view', [
			'model' => $this->findModel($team_id, $adjudicator_id),
		]);
	}

	public function actionImport($clash = null, $action = null)
	{
		//Yii::$app->request->isAjax &&
		if ($action != null && $clash != null) {
			$userClash = UserClash::findOne($clash);
			if ($userClash instanceof UserClash) {

				if ($action == "accept") {
					$decision = 1;
				} elseif ($action == "deny") {
					$decision = 0;
				} else {
					throw new Exception("Invalid action Parameter");
				}

				$clashObj = $userClash->getClashedObject($this->_tournament->id);
				$ownObj = $userClash->getOwnObject($this->_tournament->id);
				switch (get_class($clashObj)) {
					case Team::className():
						$strike = new TeamStrike([
							"team_id"        => $clashObj->id,
							"adjudicator_id" => $ownObj->id,
							"tournament_id"  => $this->_tournament->id,
							"user_clash_id"  => $userClash->id,
							"accepted"       => $decision,
						]);
						break;
					case Adjudicator::className():
						$strike = new AdjudicatorStrike([
							"adjudicator_from_id" => $ownObj->id,
							"adjudicator_to_id"   => $clashObj->id,
							"tournament_id"       => $this->_tournament->id,
							"user_clash_id"       => $userClash->id,
							"accepted"            => $decision,
						]);
						break;
					default:
						throw new Exception("No Clash Class found");
				}

				if (!$strike->save()) {
					throw new Exception(Yii::t("app", "Can't save clash decision. {reason}", [
						"reason" => ObjectError::getMsg($strike)
					]));
				}
			}
		}

		$clashes = [];
		$adjus = Adjudicator::find()->tournament($this->_tournament->id)->all();
		foreach ($adjus as $j) {
			foreach ($j->user->clashes as $c) {
				$c_a = Adjudicator::find()->tournament($this->_tournament->id)->where(["user_id" => $c->clash_with])->one();
				if ($c_a instanceof Adjudicator) {
					$already = AdjudicatorStrike::find()->where([
						"tournament_id"       => $this->_tournament->id,
						"adjudicator_from_id" => $j->id,
						"adjudicator_to_id"   => $c_a->id,
					])->count();

					if ($already === 0)
						$clashes[] = $c;

				} else { //No Adjudicator ... id might belong to a Team
					$c_a = Team::find()->tournament($this->_tournament->id)->where("speakerA_id = :user OR speakerB_id = :user", [
						"user" => $c->clash_with,
					])->one();
					if ($c_a instanceof Team) {
						$already = TeamStrike::find()->where([
							"tournament_id"  => $this->_tournament->id,
							"team_id"        => $c_a->id,
							"adjudicator_id" => $j->id,
						])->count();

						if ($already === 0)
							$clashes[] = $c;
					}
				}

			}
		}

		$team = Team::find()->tournament($this->_tournament->id)->all();
		foreach ($team as $t) {
			if ($t->speakerA) {
				foreach ($t->speakerA->clashes as $c) {
					$c_a = Adjudicator::find()->tournament($this->_tournament->id)->where(["user_id" => $c->clash_with])->one();
					if ($c_a instanceof Adjudicator) {
						$already = TeamStrike::find()->where([
							"tournament_id"  => $this->_tournament->id,
							"team_id"        => $t->id,
							"adjudicator_id" => $c_a->id,
						])->count();

						if ($already === 0)
							$clashes[] = $c;
					} // No Team2Team Clash :D
				}
			}
			if ($t->speakerB) {
				foreach ($t->speakerB->clashes as $c) {
					$c_a = Adjudicator::find()->tournament($this->_tournament->id)->where(["user_id" => $c->clash_with])->one();
					if ($c_a instanceof Adjudicator) {
						$already = TeamStrike::find()->where([
							"tournament_id"  => $this->_tournament->id,
							"team_id"        => $t->id,
							"adjudicator_id" => $c_a->id,
						])->count();

						if ($already === 0)
							$clashes[] = $c;
					} // No Team2Team Clash :D
				}
			}
		}

		$dataProvider = new ArrayDataProvider([
			'allModels' => $clashes,
		]);

		return $this->render('import', [
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Creates a new Team Strikes model.
	 * If creation is successful, the browser will be redirected to the 'index' page.
	 *
	 * @return mixed
	 */
	public function actionTeam_create()
	{
		$model = new TeamStrike();
		$model->tournament_id = $this->_tournament->id;
		$model->accepted = 1;

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['team_index', "tournament_id" => $this->_tournament->id]);
		} else {
			return $this->render('team_create', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Creates a new Adjudicator Strikes model.
	 * If creation is successful, the browser will be redirected to the 'index' page.
	 *
	 * @return mixed
	 */
	public function actionAdjudicator_create()
	{
		$model = new AdjudicatorStrike();
		$model->tournament_id = $this->_tournament->id;
		$model->accepted = 1;

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['adjudicator_index', "tournament_id" => $this->_tournament->id]);
		} else {
			return $this->render('adjudicator_create', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Updates an existing Strikes model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $team_id
	 * @param integer $adjudicator_id
	 *
	 * @return mixed
	 */
	public function actionTeam_update($team_id, $adjudicator_id)
	{
		$model = $this->findTeamModel($team_id, $adjudicator_id);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['team_index', 'tournament_id' => $model->tournament_id]);
		} else {
			return $this->render('team_update', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Finds the Strikes model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $team_id
	 * @param integer $adjudicator_id
	 *
	 * @return Strikes the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findTeamModel($team_id, $adjudicator_id)
	{
		if (($model = TeamStrike::findOne(['team_id' => $team_id, 'adjudicator_id' => $adjudicator_id])) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	/**
	 * Updates an existing Strikes model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $team_id
	 * @param integer $adjudicator_id
	 *
	 * @return mixed
	 */
	public function actionAdjudicator_update($adjudicator_from_id, $adjudicator_to_id)
	{
		$model = $this->findAdjudicatorModel($adjudicator_from_id, $adjudicator_to_id);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['adjudicator_index', 'tournament_id' => $model->tournament_id]);
		} else {
			return $this->render('adjudicator_update', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Finds the Strikes model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $adjudicator_from_id
	 * @param integer $adjudicator_to_id
	 *
	 * @return Strikes the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findAdjudicatorModel($adjudicator_from_id, $adjudicator_to_id)
	{
		if (($model = AdjudicatorStrike::findOne(['adjudicator_from_id' => $adjudicator_from_id, 'adjudicator_to_id' => $adjudicator_to_id])) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	/**
	 * Deletes an existing Strikes model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $team_id
	 * @param integer $adjudicator_id
	 *
	 * @return mixed
	 */
	public function actionTeam_delete($team_id, $adjudicator_id)
	{
		$this->findTeamModel($team_id, $adjudicator_id)->delete();

		return $this->redirect(['team_index', "tournament_id" => $this->_tournament->id]);
	}

	/**
	 * Deletes an existing Strikes model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $adjudicator_from_id
	 * @param integer $adjudicator_to_id
	 *
	 * @return mixed
	 */
	public function actionAdjudicator_delete($adjudicator_from_id, $adjudicator_to_id)
	{
		$this->findAdjudicatorModel($adjudicator_from_id, $adjudicator_to_id)->delete();

		return $this->redirect(['adjudicator_index', "tournament_id" => $this->_tournament->id]);
	}
}
