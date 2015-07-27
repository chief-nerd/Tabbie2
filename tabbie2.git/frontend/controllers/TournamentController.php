<?php

namespace frontend\controllers;

use common\components\filter\TournamentContextFilter;
use common\components\ObjectError;
use common\components\TabbieExport;
use common\models;
use common\models\search\TournamentSearch;
use common\models\Tournament;
use frontend\models\DebregsyncForm;
use kartik\helpers\Html;
use Yii;
use yii\base\Model;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\HtmlPurifier;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * TournamentController implements the CRUD actions for Tournament model.
 */
class TournamentController extends BaseTournamentController
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
					[
						'allow' => true,
						'actions' => ['index', 'archive', 'view', 'testimport', 'list'],
						'roles' => [],
					],
					[
						'allow'   => true,
						'actions' => ['create'],
						'roles'   => ['@'],
					],
					[
						'allow'         => true,
						'actions'       => ['update', 'debreg-sync'],
						'matchCallback' => function ($rule, $action) {
							return ($this->_tournament->isTabMaster(Yii::$app->user->id) || $this->_tournament->isConvenor(Yii::$app->user->id));
						}
					],
					[
						'allow'         => true,
						'actions'       => ['migrate-tabbie', 'download-sql'],
						'matchCallback' => function ($rule, $action) {
							return ($this->_tournament->isTabMaster(Yii::$app->user->id));
						}
					],
				],
			],
			'verbs'            => [
				'class'   => VerbFilter::className(),
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
	public function actionIndex()
	{
		$searchModel = new TournamentSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel'  => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Lists all Tournament models.
	 *
	 * @return mixed
	 */
	public function actionArchive()
	{
		$searchModel = new TournamentSearch();
		$dataProvider = $searchModel->searchArchive(Yii::$app->request->queryParams);

		return $this->render('archive', [
			'searchModel'  => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a current Tournament model.
	 *
	 * @return mixed
	 */
	public function actionView($id)
	{
		return $this->render('view', ['model' => $this->findModel($id),]);
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
	protected function findModel($id)
	{
		if (($model = Tournament::findByPk($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	/**
	 * Creates a new Tournament model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate()
	{
		$model = new Tournament();
		$model->status = Tournament::STATUS_RUNNING;

		if (Yii::$app->request->isPost) {
			$file = UploadedFile::getInstance($model, 'logo');
			$model->load(Yii::$app->request->post());
			$model->generateUrlSlug();
			if ($file instanceof UploadedFile) {
				$model->saveLogo($file);
			} else
				$model->logo = null;

			if ($model->save()) {
				$energyConf = new models\EnergyConfig();
				if ($energyConf->setup($model))
					Yii::$app->session->addFlash("success", Yii::t("app", "Tournament successfully created"));
				else
					Yii::$app->session->addFlash("warning", Yii::t("app", "Tournament created but Energy config failed!") . ObjectError::getMsg($energyConf));

				return $this->redirect(['view', 'id' => $model->id]);
			} else {
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
	public function actionUpdate($id)
	{
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
			} else
				$model->logo = $oldFile;

			if ($model->save()) {

				$convenors = Yii::$app->request->post("Tournament")['convenors'];
				if (count($convenors) == 0)
					$convenors = [Yii::$app->user->id];
				models\Convenor::deleteAll(["tournament_id" => $model->id]);
				foreach ($convenors as $user_id) {
					$con = new models\Convenor([
						"user_id"       => $user_id,
						"tournament_id" => $model->id
					]);
					$con->save();
				}

				$cas = Yii::$app->request->post("Tournament")['cAs'];
				if (count($cas) == 0)
					$cas = [Yii::$app->user->id];
				models\Ca::deleteAll(["tournament_id" => $model->id]);
				foreach ($cas as $user_id) {
					$ca = new models\Ca([
						"user_id"       => $user_id,
						"tournament_id" => $model->id
					]);
					$ca->save();
				}

				$tabmasters = Yii::$app->request->post("Tournament")['tabmasters'];
				if (count($tabmasters) == 0)
					$tabmasters = [Yii::$app->user->id];
				models\Tabmaster::deleteAll(["tournament_id" => $model->id]);
				foreach ($tabmasters as $user_id) {
					$tab = new models\Tabmaster([
						"user_id"       => $user_id,
						"tournament_id" => $model->id
					]);
					$tab->save();
				}

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
	public function actionDelete($id)
	{
		$this->findModel($id)->delete();

		return $this->redirect(['index']);
	}

	/**
	 * @param Tournament $tournament
	 *
	 * @return int|false
	 */
	public function activeInputAvailable($tournament)
	{
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
	public function actionDebregSync($id)
	{
		set_time_limit(0); //Prevent timeout ... this can take time

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
				} else
					return $this->render('sync_resolve', [
						'unresolved' => $unresolved,
						'tournament' => $tournament,
						'model'      => $model
					]);
			} else {
				$model->addError("password", $error);
			}

		}

		return $this->render('sync_login', [
			'model'      => $model,
			'tournament' => $tournament]);
	}

	/**
	 * Migrate back to Tabbie v1
	 *
	 * @param    integer $id
	 */
	public function actionMigrateTabbie($id)
	{
		/** Make output UTF-8 */
		mb_internal_encoding('UTF-8');
		mb_http_output('UTF-8');
		mb_http_input('UTF-8');
		mb_language('uni');
		mb_regex_encoding('UTF-8');
		ob_start('mb_output_handler');

		$export = new TabbieExport();
		echo implode("<br>\n", $export->generateV1SQL($this->_tournament));
		exit();
	}

	/**
	 * Migrate back to Tabbie v1
	 *
	 * @param    integer $id
	 */
	public function actionDownloadSql($id)
	{
		/** Make output UTF-8 */
		mb_internal_encoding('UTF-8');
		mb_http_output('UTF-8');
		mb_http_input('UTF-8');
		mb_language('uni');
		mb_regex_encoding('UTF-8');
		ob_start('mb_output_handler');

		$export = new TabbieExport();
		echo implode("<br>\n", $export->generateSQL($this->_tournament));
		exit();
	}

	/**
	 * Returns 20 societies in an JSON List
	 *
	 * @param type $search
	 * @param type $sid
	 */
	public function actionList(array $search = null, $tid = null)
	{
		$search["term"] = HtmlPurifier::process($search["term"]);
		$tid = intval($tid);

		$out = ['more' => false];
		if (!is_null($search["term"]) && $search["term"] != "" && $search["term"] != "null") {
			$query = new \yii\db\Query;
			$query->select(["id", "CONCAT(name,' ',SUBSTRING(start_date, 1,4)) as text"])
				->from('tournament')
				->andWhere(["LIKE", "name", $search["term"]])
				->limit(20);
			$command = $query->createCommand();
			$data = $command->queryAll();
			$out['results'] = array_values($data);
		} elseif ($tid > 0) {
			$out['results'] = ['id' => $tid, 'text' => Tournament::findOne($tid)->fullname];
		} else {
			$out['results'] = ['id' => 0, 'text' => "No results found"];
		}
		echo \yii\helpers\Json::encode($out);
	}
}
