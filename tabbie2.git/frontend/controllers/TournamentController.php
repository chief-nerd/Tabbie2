<?php

namespace frontend\controllers;

use common\components\filter\TournamentContextFilter;
use common\components\ObjectError;
use common\models;
use common\models\search\TournamentSearch;
use common\models\Tournament;
use frontend\models\DebregsyncForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * TournamentController implements the CRUD actions for Tournament model.
 */
class TournamentController extends BaseTournamentController {

	public function behaviors() {
		return [
			'tournamentFilter' => [
				'class' => TournamentContextFilter::className(),
			],
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						'actions' => ['index', 'archive', 'view', 'testimport'],
						'roles' => [],
					],
					[
						'allow' => true,
						'actions' => ['create'],
						'roles' => ['@'],
					],
					[
						'allow' => true,
						'actions' => ['update', 'debreg-sync'],
						'matchCallback' => function ($rule, $action) {
							return ($this->_tournament->isTabMaster(Yii::$app->user->id) || $this->_tournament->isConvenor(Yii::$app->user->id));
						}
					],
					[
						'allow' => true,
						'actions' => ['migrate-tabbie'],
						'matchCallback' => function ($rule, $action) {
							return ($this->_tournament->isTabMaster(Yii::$app->user->id));
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
	 * Lists all Tournament models.
	 *
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new TournamentSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Lists all Tournament models.
	 *
	 * @return mixed
	 */
	public function actionArchive() {
		$searchModel = new TournamentSearch();
		$dataProvider = $searchModel->searchArchive(Yii::$app->request->queryParams);

		return $this->render('archive', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a current Tournament model.
	 *
	 * @return mixed
	 */
	public function actionView($id) {
		return $this->render('view', ['model' => $this->findModel($id),]);
	}

	/**
	 * Creates a new Tournament model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate() {
		$model = new Tournament();
		$model->status = Tournament::STATUS_RUNNING;

		if (Yii::$app->request->isPost) {
			$file = UploadedFile::getInstance($model, 'logo');
			$model->load(Yii::$app->request->post());
			$model->generateUrlSlug();
			if ($file instanceof UploadedFile) {
				$model->saveLogo($file);
			}
			else
				$model->logo = null;

			if ($model->save()) {
				$energyConf = new models\EnergyConfig();
				if ($energyConf->setup($model))
					Yii::$app->session->addFlash("success", Yii::t("app", "Tournament successfully created"));
				else
                    Yii::$app->session->addFlash("warning", Yii::t("app", "Tournament created but Energy config failed!") . ObjectError::getMsg($energyConf));

				return $this->redirect(['view', 'id' => $model->id]);
			}
			else {
                Yii::$app->session->setFlash("error", Yii::t("app", "Can't save Tournament!") . ObjectError::getMsg($model));
			}
		}
		//Preset variables
		$model->tabmaster_user_id = Yii::$app->user->id;
		$model->tabAlgorithmClass = Yii::$app->params["stdTabAlgorithm"];
		return $this->render('create', ['model' => $model,]);
	}

	/**
	 * Updates an existing Tournament model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionUpdate($id) {
		$model = $this->findModel($id);

		if (Yii::$app->request->isPost) {

			//Upload File
			$file = \yii\web\UploadedFile::getInstance($model, 'logo');

			//Save Old File Path
			$oldFile = $model->logo;
			//Load new values
			$model->load(Yii::$app->request->post());

			if ($file instanceof UploadedFile) {
				//Save new File
				$model->saveLogo($file);
			}
			else
				$model->logo = $oldFile;

			if ($model->save()) {
				Yii::$app->cache->set("tournament" . $model->id, $model, 120);
				return $this->redirect(['view', 'id' => $model->id]);
			}
		}

		return $this->render('update', ['model' => $model,]);
	}

	/**
	 * Deletes an existing Tournament model.
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
	 * Finds the Tournament model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 *
	 * @return Tournament the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id) {
		if (($model = Tournament::findByPk($id)) !== null) {
			return $model;
		}
		else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	/**
	 * @param Tournament $tournament
	 *
	 * @return int|false
	 */
	public function activeInputAvailable($tournament) {
		$user_id = Yii::$app->user->id;

		$activeRound = models\Round::findOne(["tournament_id" => $tournament->id, "displayed" => 1, "published" => 1, "closed" => 0,]);

		if ($activeRound) {
			$debate = models\Debate::findOneByChair($user_id, $tournament->id, $activeRound->id);
			if ($debate instanceof models\Debate) return $debate->id;
		}

		return false;
	}


	/**
	 * Sync with DebReg System
	 *
	 * @param integer $id
	 *
	 * @return string|\yii\web\Response
	 * @throws \yii\web\NotFoundHttpException
	 */
	public function actionDebregSync($id) {

		$tournament = $this->findModel($id);
		$model = new DebregsyncForm();

		if (Yii::$app->request->isPost) {

			$a_fix = $t_fix = $s_fix = [];

			if (Yii::$app->request->post("mode") == "refactor") {
				$a_fix = Yii::$app->request->post("Adju", []);
				$t_fix = Yii::$app->request->post("Team", []);
				$s_fix = Yii::$app->request->post("Soc", []);
			}

			$model->load(Yii::$app->request->post());
			$model->tournament = $this->_tournament;

			$error = $model->getAccessKey();

			if ($error === true) {
				$unresolved = $model->doSync($a_fix, $t_fix, $s_fix);

				if (count($unresolved) == 0) {
					Yii::$app->session->addFlash("success", Yii::t("app", "DebReg Syncing successful"));
					return $this->redirect(['view', 'id' => $tournament->id]);
				}
				else
					return $this->render('sync_resolve', [
						'unresolved' => $unresolved,
						'tournament' => $tournament,
						'model' => $model
					]);
			}
			else {
				$model->addError("password", $error);
			}

		}

		return $this->render('sync_login', [
			'model' => $model,
			'tournament' => $tournament]);
	}

	/**
	 * Migrate back to Tabbie v1
	 *
	 * @param    integer $id
	 */
	public function actionMigrateTabbie($id) {

        $tournament = $this->_tournament;

		$sqlFile = [];
		$sqlFile[] = "USE database tabbie;";

        /** ADJUDICATORS */
        $sqlFile[] = "CREATE TABLE adjudicators";

        $adju = models\Adjudicator::find()->tournament($tournament->id)->all();
        foreach ($adju as $a) {
            $sqlFile[] = "INSERT INTO adjudicator VALUES(" . implode(",", [$a->id, $this->strquote($a->user->name)]) . ");";
        }

        /** TEAMS */
        $sqlFile[] = "CREATE TABLE teams";

        $teams = models\Team::find()->tournament($tournament->id)->all();
        foreach ($teams as $t) {
            $sqlFile[] = "INSERT INTO teams VALUES();";
        }

        /** Venues */
        $sqlFile[] = "CREATE TABLE venues";

        $venues = models\Venue::find()->tournament($tournament->id)->all();
        foreach ($venues as $v) {
            $sqlFile[] = "INSERT INTO venue VALUES();";
        }

        foreach ($tournament->rounds as $round) {
            /** ROUND */
            /** @var models\Round $round */

            $sqlFile[] = "CREATE TABLE round_" . $round->number . " ";
            $sqlFile[] = "CREATE TABLE result_round_" . $round->number . " ";

            foreach ($round->debates as $debate) {
                /** DEBATE */
                /** @var models\Debate $debate */


                /** RESULT */
				if ($debate->result instanceof models\Result) // There might not be a result yet
				{
                    /** @var models\Result $result */
					$result = $debate->result;

                    $values = [$debate->id];
                    $values[$result->og_place] = $debate->og_team_id;
                    $values[$result->oo_place] = $debate->oo_team_id;
                    $values[$result->cg_place] = $debate->cg_team_id;
                    $values[$result->co_place] = $debate->co_team_id;

                    $sqlFile[] = "INSERT INTO result_round_" . $round->number . " VALUES(" . implode(",", $values) . ")";
				}
			}
		}

		echo implode("<br>\n", $sqlFile);
		exit();
	}

    private function strquote($str)
    {
        return "'" . $str . "'";
    }
}
