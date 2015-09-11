<?php

namespace frontend\controllers;

use common\components\filter\TournamentContextFilter;
use common\components\ObjectError;
use common\models\Debate;
use common\models\Round;
use common\models\search\TeamSearch;
use common\models\Team;
use common\models\Tournament;
use common\models\User;
use Yii;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * TeamController implements the CRUD actions for Team model.
 */
class TeamController extends BasetournamentController
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
						'allow'         => true,
						'actions'       => ['view'],
						'matchCallback' => function ($rule, $action) {
							return ($this->_tournament->status >= Tournament::STATUS_CLOSED && !Yii::$app->user->isGuest);
						}
					],
					[
						'allow' => true,
						'actions'       => ['index', 'view'],
						'matchCallback' => function ($rule, $action) {
							return ($this->_tournament->isTabMaster(Yii::$app->user->id) ||
								$this->_tournament->isConvenor(Yii::$app->user->id) ||
								$this->_tournament->isCA(Yii::$app->user->id));
						}
					],
					[
						'allow'         => true,
						'actions'       => ['create', 'update', 'delete', 'import', 'active', 'list'],
						'matchCallback' => function ($rule, $action) {
							return ($this->_tournament->isTabMaster(Yii::$app->user->id) ||
								$this->_tournament->isCA(Yii::$app->user->id));
						}
					],
				],
			],
		];
	}

	/**
	 * Displays a single Team model.
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionView($id)
	{
		$model = $this->findModel($id);
		$query = Debate::find()->joinWith("round")->andWhere(["debate.tournament_id" => $model->tournament_id])
			->andWhere("og_team_id = :id OR oo_team_id = :id OR cg_team_id = :id OR co_team_id = :id", [":id" => $id])
			->andWhere(["round.displayed" => 1])
			->orderBy(["id" => SORT_DESC]);

		$dataRoundsProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		return $this->render('view', [
			'model'              => $model,
			'dataRoundsProvider' => $dataRoundsProvider,
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
	protected function findModel($id)
	{
		if (($model = Team::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	/**
	 * Toggle a Team visability
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionActive($id)
	{
		$model = $this->findModel($id);

		if ($model->active == 0)
			$model->active = 1;
		else {
			$model->active = 0;
		}

		if (!$model->save()) {
			Yii::$app->session->addFlash("error", ObjectError::getMsg($model));
		}

		$model->refresh();
		if (Yii::$app->request->isAjax) {
			unset($_GET["id"]);
			$this->runAction("index");
		} else
			return $this->redirect(['team/index', 'tournament_id' => $this->_tournament->id]);
	}

	/**
	 * Lists all Team models.
	 *
	 * @return mixed
	 */
	public function actionIndex()
	{
		$searchModel = new TeamSearch(["tournament_id" => $this->_tournament->id]);
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		$stat["active"] = Team::find()->active()->tournament($this->_tournament->id)->count();
		$stat["inactive"] = Team::find()->active(false)->tournament($this->_tournament->id)->count();
		$stat["swing"] = Team::find()->tournament($this->_tournament->id)->andWhere(["isSwing" => true])->count();

		return $this->render('index', [
			'searchModel'  => $searchModel,
			'dataProvider' => $dataProvider,
			'stat'         => $stat,
		]);
	}

	/**
	 * Creates a new Team model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate()
	{
		$model = new Team();
		$model->tournament_id = $this->_tournament->id;

		if ($model->load(Yii::$app->request->post())) {

			if ($model->isSwing == 1) {
				if (empty($model->speakerA_id))
					$model->speakerA_id = \common\models\User::generatePlaceholder($this->_tournament->url_slug)->id;
				if (empty($model->speakerB_id))
					$model->speakerB_id = \common\models\User::generatePlaceholder($this->_tournament->url_slug)->id;
			}

			if ($model->save())
				return $this->redirect(['view', 'id' => $model->id, 'tournament_id' => $model->tournament_id]);
			else {
				\Yii::error("Error saving Team: " . ObjectError::getMsg($model), __METHOD__);
				\Yii::$app->session->addFlash("error", Yii::t("app", "Couldn't create Team."));
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
	public function actionUpdate($id)
	{
		$model = $this->findModel($id);

		if ($model->load(Yii::$app->request->post())) {

			if ($model->isSwing == 1) {
				if (empty($model->speakerA_id))
					$model->speakerA_id = \common\models\User::generatePlaceholder($this->_tournament->url_slug)->id;
				if (empty($model->speakerB_id))
					$model->speakerB_id = \common\models\User::generatePlaceholder($this->_tournament->url_slug)->id;
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
	public function actionDelete($id)
	{
		$model = $this->findModel($id);

		if ($model->isSwing) { //isSwing -> clean up Placeholder
			$model->speakerA->delete();
			$model->speakerB->delete();
		}
		$model->delete();

		return $this->redirect(['team/index', 'tournament_id' => $this->_tournament->id]);
	}

	public function actionImport()
	{
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
					if (!isset($model->tempImport[$r])) continue;
					$row = $model->tempImport[$r];

					//Society
					$temp_society = \common\models\Society::findOne(["fullname" => $row[1][0]]);
					if ($temp_society instanceof Society)
						$societyID = $temp_society->id;
					else
						$societyID = null;

					if (count($row[1]) == 1 && is_null($societyID)) { //NEW
						$society = new \common\models\Society();
						$society->fullname = $row[1][0];
						$society->abr = \common\models\Society::generateAbr($society->fullname);
						$society->country_id = \common\models\Country::COUNTRY_UNKNOWN_ID;
						if (!$society->save()) {
							Yii::error("Import Errors Society: " . ObjectError::getMsg($model), __METHOD__);
							Yii::$app->session->addFlash("error", Yii::t("app", "Error saving Society Relation for {society}", ["society" => $society->fullname]));
						}
						$societyID = $society->id;
					} else if (count($row[1]) == 2 && is_null($societyID)) {
						$societyID = $row[1][1]["id"];
					}

					//UserA
					if (count($row[2]) == 1) { //NEW
						if ($row[2][0] != "" && $row[3][0] != "" && $row[4][0] != "") {
							$userA = User::NewViaImport($row[2][0], $row[3][0], $row[4][0], $societyID, !$model->is_test, $this->_tournament);
							$userAID = $userA->id;
						} else
							$userAID = null;
					} else if (count($row[2]) == 2) {
						$userAID = $row[2][1]["id"];
					}

					//UserB
					if (count($row[5]) == 1) { //NEW
						if ($row[5][0] != "" && $row[6][0] != "" && $row[7][0] != "") {
							$userB = User::NewViaImport($row[5][0], $row[6][0], $row[7][0], $societyID, !$model->is_test, $this->_tournament);
							$userBID = $userB->id;
						} else
							$userBID = null;
					} else if (count($row[5]) == 2) {
						$userBID = $row[5][1]["id"];
					}

					if (Team::find()
							->tournament($this->_tournament->id)
							->andWhere(['speakerA_id' => $userAID, 'speakerB_id' => $userBID])
							->count() == 0
					) {
						$team = new Team();
						$team->name = $row[0][0];
						$team->tournament_id = $this->_tournament->id;
						$team->speakerA_id = $userAID;
						$team->speakerB_id = $userBID;
						$team->society_id = $societyID;
						if (!$team->save(false)) {
							Yii::$app->session->addFlash("error", Yii::t("app", "Error saving team {name}!", ["{name}" => $team->name]));
							Yii::error("Import Errors userB: " . ObjectError::getMsg($model) . "Attributes:" . print_r($team->attributes, true), __METHOD__);
						}
					}
				}
				set_time_limit(30);

				return $this->redirect(['index', "tournament_id" => $this->_tournament->id]);
			} else { //FORM UPLOAD
				$file = \yii\web\UploadedFile::getInstance($model, 'csvFile');
				$model->load(Yii::$app->request->post());

				$row = 0;
				if ($file && ($handle = fopen($file->tempName, "r")) !== false) {
					while (($data = fgetcsv($handle, null, $model->delimiter)) !== false) {

						if ($row == 0) { //Don't use first column
							$row++;
							continue;
						}

						if (($num = count($data)) != 8) {
							Yii::$app->session->addFlash("error", Yii::t("app", "File Syntax Wrong"));
							return $this->redirect(['import', "tournament_id" => $this->_tournament->id]);
						}
						for ($c = 0; $c < $num; $c++) {
							$model->tempImport[$row][$c][0] = utf8_encode(trim($data[$c]));
						}
						$row++;
					}
					fclose($handle);

					//Find Matches
					for ($i = 1; $i <= count($model->tempImport); $i++) {

						if (!isset($model->tempImport[$i])) continue;

						$name = $model->tempImport[$i][1][0];

						//TeamName - not match
						//
						//Debating Society
						$societies = \common\models\Society::find()->where(["like", "fullname", $name])->orWhere(["abr" => $name])->all();
						$model->tempImport[$i][1] = [];
						$model->tempImport[$i][1][0] = $name;
						$a = 1;
						foreach ($societies as $s) {
							$model->tempImport[$i][1][$a] = [
								"id"   => $s->id,
								"name" => $s->fullname,
							];
							$a++;
						}

						//User A
						$givenname = $model->tempImport[$i][2][0];
						$surename = $model->tempImport[$i][3][0];
						$email = $model->tempImport[$i][4][0];

						$user = \common\models\User::find()
							->where("(givenname = '$givenname' AND surename = '$surename') OR email = '$email'")
							->all();
						$a = 1;
						foreach ($user as $u) {
							$model->tempImport[$i][2][$a] = [
								"id"    => $u->id,
								"name"  => $u->name,
								"email" => $u->email,
							];
							$a++;
						}

						//User B
						$givenname = $model->tempImport[$i][5][0];
						$surename = $model->tempImport[$i][6][0];
						$email = $model->tempImport[$i][7][0];

						$user = \common\models\User::find()
							->where("(givenname = '$givenname' AND surename = '$surename') OR email = '$email'")
							->all();
						$a = 1;
						foreach ($user as $u) {
							$model->tempImport[$i][5][$a] = [
								"id"    => $u->id,
								"name"  => $u->name,
								"email" => $u->email,
							];
							$a++;
						}
					}
				} else {
					Yii::$app->session->addFlash("error", Yii::t("app", "No File available"));
				}
			}
		} else
			$model->scenario = "upload";

		return $this->render('import', [
			"model"      => $model,
			"tournament" => $tournament
		]);
	}

	/**
	 * Returns 20 Teams in an JSON List
	 *
	 * @param type $search
	 * @param type $id
	 */
	public function actionList(array $search = null, $id = null, $tournament_id)
	{
		$out = ['more' => false];
		if (!is_null($search["term"]) && $search["term"] != "") {
			$query = new \yii\db\Query;
			$query->select(["id", "name as text"])
				->from('team')
				->where('tournament_id = "' . $tournament_id . '" AND name LIKE "%' . $search["term"] . '%"')
				->limit(20);
			$command = $query->createCommand();
			$data = $command->queryAll();
			$out['results'] = array_values($data);
		} elseif ($id > 0) {
			$out['results'] = ['id' => $id, 'text' => Team::findOne($id)->name];
		} else {
			$out['results'] = ['id' => 0, 'text' => Yii::t("app", 'No matching records found')];
		}
		echo \yii\helpers\Json::encode($out);
	}

}
