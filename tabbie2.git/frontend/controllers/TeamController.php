<?php

namespace frontend\controllers;

use common\components\filter\TournamentContextFilter;
use common\models\Country;
use common\models\search\TeamSearch;
use common\models\Team;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * TeamController implements the CRUD actions for Team model.
 */
class TeamController extends BaseTournamentController {

	public function behaviors() {
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						'actions' => ['index', 'view'],
						'matchCallback' => function ($rule, $action) {
							return (Yii::$app->user->isTabMaster($this->_tournament) || Yii::$app->user->isConvenor($this->_tournament));
						}
					],
					[
						'allow' => true,
						'actions' => ['create', 'update', 'delete', 'import', 'active', 'list'],
						'matchCallback' => function ($rule, $action) {
							return (Yii::$app->user->isTabMaster($this->_tournament));
						}
					],
				],
			],
			'tournamentFilter' => [
				'class' => TournamentContextFilter::className(),
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
	 * Lists all Team models.
	 *
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new TeamSearch(["tournament_id" => $this->_tournament->id]);
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		$stat["active"] = Team::find()->active()->tournament($this->_tournament->id)->count();
		$stat["inactive"] = Team::find()->active(false)->tournament($this->_tournament->id)->count();
		$stat["swing"] = Team::find()->tournament($this->_tournament->id)->andWhere(["isSwing" => true])->count();

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
			'stat' => $stat,
		]);
	}

	/**
	 * Displays a single Team model.
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionView($id) {
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	/**
	 * Toggle a Team visability
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionActive($id) {
		$model = $this->findModel($id);

		if ($model->active == 0)
			$model->active = 1;
		else {
			$model->active = 0;
		}

		if (!$model->save()) {
			Yii::$app->session->addFlash("error", $model->getErrors("active"));
		}

		return $this->redirect(['team/index', 'tournament_id' => $this->_tournament->id]);
	}

	/**
	 * Creates a new Team model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate() {
		$model = new Team();
		$model->tournament_id = $this->_tournament->id;

		if ($model->load(Yii::$app->request->post())) {

			if ($model->isSwing == 1) {
				if (empty($model->speakerA_id))
					$model->speakerA_id = \common\models\User::generatePlaceholder("A")->id;
				if (empty($model->speakerB_id))
					$model->speakerB_id = \common\models\User::generatePlaceholder("B")->id;
			}

			if ($model->save())
				return $this->redirect(['view', 'id' => $model->id, 'tournament_id' => $model->tournament_id]);
			else {
				\Yii::error("Error saving Team: " . print_r($model->getErrors(), true), __METHOD__);
				\Yii::$app->session->addFlash("error", "Couldn't create Team.");
			}
		}

		$model->active = 1; //Set default;
		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing Team model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionUpdate($id) {
		$model = $this->findModel($id);

		if ($model->load(Yii::$app->request->post())) {

			if ($model->isSwing == 1) {
				if (empty($model->speakerA_id))
					$model->speakerA_id = \common\models\User::generatePlaceholder("A")->id;
				if (empty($model->speakerB_id))
					$model->speakerB_id = \common\models\User::generatePlaceholder("B")->id;
			}
			if ($model->save())
				return $this->redirect(['index', 'tournament_id' => $model->tournament_id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing Team model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionDelete($id) {
		$model = $this->findModel($id);

		if ($model->isSwing) { //isSwing -> clean up Placeholder
			$model->speakerA->delete();
			$model->speakerB->delete();
		}
		$model->delete();
		return $this->redirect(['team/index', 'tournament_id' => $this->_tournament->id]);
	}

	public function actionImport() {
		$tournament = $this->_tournament;
		$model = new \frontend\models\ImportForm();

		if (Yii::$app->request->isPost) {
			$model->scenario = "screen";
			if (Yii::$app->request->post("makeItSo", false)) { //Everything corrected
				set_time_limit(0);
				$choices = Yii::$app->request->post("field", false);
				$model->tempImport = unserialize(Yii::$app->request->post("csvFile", false));

				//APPLY CHOICES
				if (is_array($choices)) {
					foreach ($choices as $row => $choice) {
						foreach ($choice as $id => $value) {
							$input = $model->tempImport[$row][$id][0];
							unset($model->tempImport[$row][$id]);
							$model->tempImport[$row][$id][0] = $input;
							$model->tempImport[$row][$id][1]["id"] = $value;
						}
					}
				}

				//INSERT DATA
				for ($r = 1; $r <= count($model->tempImport); $r++) {
					$row = $model->tempImport[$r];

					$societyID = null;

					//Society
					if (count($row[1]) == 1) { //NEW
						$society = new \common\models\Society();
						$society->fullname = $row[1][0];
						$society->abr = \common\models\Society::generateAbr($society->fullname);
						$society->country_id = \common\models\Country::COUNTRY_UNKNOWN_ID;
						$society->save();
						$societyID = $society->id;
					}
					else if (count($row[1]) == 2) {
						$societyID = $row[1][1]["id"];
					}

					//UserA
					if (count($row[2]) == 1) { //NEW
						$userA = new \common\models\User();
						$userA->givenname = $row[2][0];
						$userA->surename = $row[3][0];
						$userA->username = $userA->givenname . $userA->surename;
						$userA->email = $row[4][0];
						$userA->setPassword($userA->email);
						$userA->generateAuthKey();
						$userA->time = $userA->last_change = date("Y-m-d H:i:s");
						if ($userA->save()) {
							$inSociety = new \common\models\InSociety();
							$inSociety->user_id = $userA->id;
							$inSociety->society_id = $societyID;
							$inSociety->starting = date("Y-m-d");
							if (!$inSociety->save()) {
								Yii::error("Import Errors inSocietyA: " . print_r($inSociety->getErrors(), true), __METHOD__);
								Yii::$app->session->addFlash("error", "Error saving InSociety Relation for " . $userA->username);
							}
							else
								Yii::trace("In Society A created" . print_r($inSociety->getErrors(), true));
						}
						else {
							Yii::error("Import Errors userA: " . print_r($userA->getErrors(), true), __METHOD__);
							Yii::$app->session->addFlash("error", "Error Saving User " . $userA->username);
						}
						$userAID = $userA->id;
					}
					else if (count($row[2]) == 2) {
						$userAID = $row[2][1]["id"];
					}

					//UserB
					if (count($row[5]) == 1) { //NEW
						$userB = new \common\models\User();
						$userB->givenname = $row[5][0];
						$userB->surename = $row[6][0];
						$userB->username = $userB->givenname . $userB->surename;
						$userB->email = $row[7][0];
						$userB->setPassword($userB->email);
						$userB->generateAuthKey();
						$userB->time = $userB->last_change = date("Y-m-d H:i:s");
						if ($userB->save()) {
							$inSociety = new \common\models\InSociety();
							$inSociety->user_id = $userB->id;
							$inSociety->society_id = $societyID;
							$inSociety->starting = date("Y-m-d");
							if (!$inSociety->save()) {
								Yii::error("Import Errors inSocietyB: " . print_r($inSociety->getErrors(), true), __METHOD__);
								Yii::$app->session->addFlash("error", "Error saving InSociety Relation for " . $userB->username);
							}
							else
								Yii::trace("In Society A created" . print_r($inSociety->getErrors(), true));

						}
						else {
							Yii::error("Import Errors userB: " . print_r($userB->getErrors(), true), __METHOD__);
							Yii::$app->session->addFlash("error", "Error Saving User " . $userB->username);
						}
						$userBID = $userB->id;
					}
					else if (count($row[5]) == 2) {
						$userBID = $row[5][1]["id"];
					}

					$team = new Team();
					$team->name = $row[0][0];
					$team->tournament_id = $this->_tournament->id;
					$team->speakerA_id = $userAID;
					$team->speakerB_id = $userBID;
					$team->society_id = $societyID;
					if (!$team->save()) {
						Yii::$app->session->addFlash("error", Yii::t("app", "Error saving team {name}!", ["{name}" => $team->name]));
						Yii::error("Import Errors userB: " . print_r($team->getErrors(), true) . "Attributes:" . print_r($team->attributes, true), __METHOD__);
					}
				}
				set_time_limit(30);
				return $this->redirect(['index', "tournament_id" => $this->_tournament->id]);
			}
			else { //FORM UPLOAD
				$file = \yii\web\UploadedFile::getInstance($model, 'csvFile');
				$model->load(Yii::$app->request->post());

				$row = 0;
				if ($file && ($handle = fopen($file->tempName, "r")) !== false) {
					while (($data = fgetcsv($handle, 1000, ";")) !== false) {

						if ($row == 0) { //Don't use first column
							$row++;
							continue;
						}

						if (($num = count($data)) != 8) {
							throw new \yii\base\Exception("500", "File Syntax Wrong");
						}
						for ($c = 0; $c < $num; $c++) {
							$model->tempImport[$row][$c][0] = trim($data[$c]);
						}
						$row++;
					}
					fclose($handle);

					//Find Matches
					for ($i = 1; $i <= count($model->tempImport); $i++) {
						//TeamName - not match
						//
						//Debating Society
						$name = $model->tempImport[$i][1][0];
						$societies = \common\models\Society::find()
						                                   ->where("fullname LIKE '%:name%'", [":name" => $name])
						                                   ->all();
						$model->tempImport[$i][1] = array();
						$model->tempImport[$i][1][0] = $name;
						$a = 1;
						foreach ($societies as $s) {
							$model->tempImport[$i][1][$a] = [
								"id" => $s->id,
								"name" => $s->fullname,
							];
							$a++;
						}

						//User A
						$givenname = $model->tempImport[$i][2][0];
						$surename = $model->tempImport[$i][3][0];
						$email = $model->tempImport[$i][4][0];
						$user = \common\models\User::find()
						                           ->where("(givenname LIKE '%$givenname%' AND surename LIKE '%$surename%') OR surename LIKE '%$email%'")
						                           ->all();
						$a = 1;
						foreach ($user as $u) {
							$model->tempImport[$i][2][$a] = [
								"id" => $u->id,
								"name" => $u->name,
								"email" => $u->email,
							];
							$a++;
						}

						//User B
						$givenname = $model->tempImport[$i][5][0];
						$surename = $model->tempImport[$i][6][0];
						$email = $model->tempImport[$i][7][0];
						$user = \common\models\User::find()
						                           ->where("(givenname LIKE '%$givenname%' AND surename LIKE '%$surename%') OR surename LIKE '%$email%'")
						                           ->all();
						$a = 1;
						foreach ($user as $u) {
							$model->tempImport[$i][5][$a] = [
								"id" => $u->id,
								"name" => $u->name,
								"email" => $u->email,
							];
							$a++;
						}
					}
				}
				else {
					Yii::$app->session->addFlash("error", "No File available");
					print_r($file);
				}
			}
		}
		else
			$model->scenario = "upload";

		return $this->render('import', [
			"model" => $model,
			"tournament" => $tournament
		]);
	}

	/**
	 * Finds the Team model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 *
	 * @return Team the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id) {
		if (($model = Team::findOne($id)) !== null) {
			return $model;
		}
		else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	/**
	 * Returns 20 Teams in an JSON List
	 *
	 * @param type $search
	 * @param type $id
	 */
	public function actionList($search = null, $id = null, $tournament_id) {
		$out = ['more' => false];
		if (!is_null($search)) {
			$query = new \yii\db\Query;
			$query->select(["id", "name as text"])
			      ->from('team')
			      ->where('tournament_id = "' . $tournament_id . '" AND name LIKE "%' . $search . '%"')
			      ->limit(20);
			$command = $query->createCommand();
			$data = $command->queryAll();
			$out['results'] = array_values($data);
		}
		elseif ($id > 0) {
			$out['results'] = ['id' => $id, 'text' => Team::findOne($id)->name];
		}
		else {
			$out['results'] = ['id' => 0, 'text' => 'No matching records found'];
		}
		echo \yii\helpers\Json::encode($out);
	}

}
