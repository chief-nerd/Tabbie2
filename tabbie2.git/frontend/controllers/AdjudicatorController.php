<?php

namespace frontend\controllers;

use common\components\filter\TournamentContextFilter;
use common\components\ObjectError;
use common\models\Adjudicator;
use common\models\Country;
use common\models\Panel;
use common\models\search\AdjudicatorSearch;
use common\models\User;
use common\models\Venue;
use common\models\Society;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * AdjudicatorController implements the CRUD actions for Adjudicator model.
 */
class AdjudicatorController extends BasetournamentController
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
						'actions'       => ['index', 'view'],
						'matchCallback' => function ($rule, $action) {
							return ($this->_tournament->isTabMaster(Yii::$app->user->id) ||
								$this->_tournament->isConvenor(Yii::$app->user->id) ||
								$this->_tournament->isCA(Yii::$app->user->id)
							);
						}
					],
					[
						'allow'         => true,
						'actions' => ['create', 'update', 'delete', 'replace', 'move', 'import', 'active', 'popup', 'watch', 'break', 'list', 'resetwatched'],
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
	 * Function called by Ajax Dgra ang Drop
	 *
	 * @return string|int
	 * @throws Exception
	 */
	public function actionReplace()
	{

		try {
			if (Yii::$app->request->isPost)
				$params = Yii::$app->request->post();
			else
				$params = Yii::$app->request->get();

			if (1 == 1) {
				$ID = $params["id"];
				$POS = $params["pos"];
				$OLD = $params["old_panel"];
				$NEW = $params["new_panel"];

				/* @var $oldPanel Panel */
				$oldPanel = Panel::findOne(["id" => $OLD]);
				/* @var $newPanel Panel */
				$newPanel = Panel::findOne(["id" => $NEW]);

				if ($oldPanel instanceof Panel && $newPanel instanceof Panel) {

					if ($POS == 0 && $oldPanel->is_chair($ID)) { // Chair -> Chair
						if ($oldPanel != $newPanel) {
							//Panel has changed
							$oldPanel->changeTo($newPanel, $ID);
							$oldPanel->setChair();
							$newPanel->setAllWings();
							$newPanel->setChair($ID);
						} else {
							//Same Panel - nothing to do
						}
					} else if ($POS > 0 && $oldPanel->is_chair($ID)) { // Chair -> Wing
						if ($OLD != $NEW) {
							$oldPanel->changeTo($newPanel, $ID);
							$oldPanel->setChair();
							$newPanel->setWing($ID);
						} else {
							$oldPanel->setChair();
							$oldPanel->setWing($ID);
						}
					} else if ($POS == 0 && !$oldPanel->is_chair($ID)) { // Wing -> Chair
						if ($OLD != $NEW) {
							$oldPanel->changeTo($newPanel, $ID);
							$oldPanel->setChair();
							$newPanel->setChair($ID);
						} else {
							$oldPanel->setChair($ID);
						}
					} else if ($POS > 0 && !$oldPanel->is_chair($ID)) { // Wing -> Wing
						if ($OLD != $NEW) {
							$oldPanel->changeTo($newPanel, $ID);
						} else {
							//nothing
						}
					} else {
						throw new Exception(Yii::t("app", "No condition matched"));
					}

					/** Recalculate Energy and Messages */

					$round = $newPanel->debate->round;
					$newLines = $round->updateEnergy(["newPanel" => $newPanel->debate->id, "oldPanel" => $oldPanel->debate->id]);

					// Refresh Values to check
					$oldPanel->refresh();
					$newPanel->refresh();
					if ($oldPanel->check() && $newPanel->check())
						return json_encode($newLines);
					else {
						$error_message = Yii::t("app", "Did not pass panel check old: {old} / new: {new}", [
							"old" => (($oldPanel->check()) ? 'true' : 'false'),
							"new" => (($newPanel->check()) ? 'true' : 'false'),
						]);
						Yii::error($error_message, __METHOD__);
						throw new Exception($error_message);
					}
				} else
					throw new Exception("No Panel");
			}
		} catch (Exception $ex) {
			/* @var $ex Exception */
			return $ex->getMessage();
		}

		return "run trough";
	}

	/**
	 * Function called when move results are sent
	 */
	public function actionMove()
	{

	}

	/**
	 * Displays a single Adjudicator model.
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionView($id)
	{
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	/**
	 * Finds the Adjudicator model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 *
	 * @return Adjudicator the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		if (($model = Adjudicator::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	/**
	 * Toggle a Adjudicator visability
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
			$this->runAction("index");
		} else
			return $this->redirect(['adjudicator/index', 'tournament_id' => $this->_tournament->id]);
	}

	/**
	 * Lists all Adjudicator models.
	 *
	 * @return mixed
	 */
	public function actionIndex()
	{
		$searchModel = new AdjudicatorSearch(["tournament_id" => $this->_tournament->id]);
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		$stat["amount"] = Adjudicator::find()->active()->tournament($this->_tournament->id)->count();
		$stat["venues"] = Venue::find()->active()->tournament($this->_tournament->id)->count();
		$stat["inactive"] = Adjudicator::find()->active(false)->tournament($this->_tournament->id)->count();

		return $this->render('index', [
			'searchModel'  => $searchModel,
			'dataProvider' => $dataProvider,
			'stat'         => $stat,
		]);
	}

	/**
	 * Creates a new Adjudicator model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate()
	{
		$model = new Adjudicator();
		$model->tournament_id = $this->_tournament->id;

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id, "tournament_id" => $model->tournament_id]);
		} else {
			return $this->render('create', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Updates an existing Adjudicator model.
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
			return $this->redirect(['index', "tournament_id" => $model->tournament_id]);
		} else {
			return $this->render('update', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Deletes an existing Adjudicator model.
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

	public function actionImport()
	{
		$tournament = $this->_tournament;
		$model = new \frontend\models\ImportForm();

		if (Yii::$app->request->isPost) {
			//$model->scenario = "screen";
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
					$temp_society = \common\models\Society::findOne(["fullname" => $row[0][0]]);
					if ($temp_society instanceof Society)
						$societyID = $temp_society->id;
					else
						$societyID = null;

					if (count($row[0]) == 1 && is_null($societyID)) { //NEW
						$society = new \common\models\Society();
						$society->fullname = $row[0][0];
						$society->abr = \common\models\Society::generateAbr($society->fullname);
						$society->country_id = \common\models\Country::COUNTRY_UNKNOWN_ID;
						$society->save();
						$societyID = $society->id;
					} else if (count($row[0]) == 2 && is_null($societyID)) {
						$societyID = $row[0][1]["id"];
					}

//User
					if (count($row[1]) == 1) { //NEW
						$userA = User::NewViaImport($row[1][0], $row[2][0], $row[3][0], $societyID, !$model->is_test, $tournament);
						$userAID = $userA->id;
					} else if (count($row[1]) == 2) {
						$userAID = $row[1][1]["id"];
					} else {
						print_r($row);
						exit();
					}

					if (Adjudicator::find()
							->tournament($this->_tournament->id)
							->andWhere(['user_id' => $userAID])
							->count() == 0
					) {
						$adj = new Adjudicator();
						$adj->user_id = $userAID;
						$adj->tournament_id = $this->_tournament->id;
						$adj->strength = intval($row[4][0]);
						$adj->society_id = $societyID;
						if (!$adj->save()) {
							Yii::$app->session->addFlash("error", Yii::t("app", "Can't save {object}! Error: {message}", [
								"object"  => Yii::t("app", "Adjudicator"),
								"message" => ObjectError::getMsg($adj)
							]));
						}
					}
				}
				set_time_limit(30);

				return $this->redirect(['index', "tournament_id" => $this->_tournament->id]);
			} else { //FORM UPLOAD
				$model->load(Yii::$app->request->post());
				$file = \yii\web\UploadedFile::getInstance($model, 'csvFile');

				$row = 0;
				ini_set("auto_detect_line_endings", true);
				if ($file && ($handle = fopen($file->tempName, "r")) !== false) {
					while (($data = fgetcsv($handle, null, $model->getDelimiterChar())) !== false) {

						if ($row == 0) { //Don't use first column
							$row++;
							continue;
						}

						if (($num = count($data)) != 5) {
							Yii::$app->session->addFlash("error", Yii::t("app", "File Syntax not matching. 5 columns required."));
							return $this->redirect(['import', "tournament_id" => $this->_tournament->id]);
						}
						$coldata = [];

						$allFieldsFilled = true;
						for ($c = 0; $c < $num; $c++) {
							$clean_data = utf8_encode(trim($data[$c]));
							if (strlen($clean_data) == 0) $allFieldsFilled = false;
							$coldata[$c][0] = $clean_data;
						}

						if ($allFieldsFilled)
							$model->tempImport[$row] = $coldata;

						$row++;
					}
					fclose($handle);

//Find Matches
					for ($i = 1; $i <= count($model->tempImport); $i++) {
//Debating Society
						if (!isset($model->tempImport[$i])) continue;

						$society_name = $model->tempImport[$i][0][0];
						$givenname = $model->tempImport[$i][1][0];
						$surename = $model->tempImport[$i][2][0];
						$email = $model->tempImport[$i][3][0];

						if (strlen($society_name) > 0 && strlen($givenname) > 0 && strlen($surename) > 0 && strlen($email) > 0) {

							$societies = \common\models\Society::find()
								->where(["like", "fullname", $society_name])->orWhere(["abr" => $society_name])
								->all();

							$model->tempImport[$i][0] = [];
							$model->tempImport[$i][0][0] = $society_name;
							$a = 1;
							foreach ($societies as $s) {
								$model->tempImport[$i][0][$a] = [
									"id"   => $s->id,
									"name" => $s->fullname,
								];
								$a++;
							}
//User A

							$user = \common\models\User::find()
								->where("(givenname LIKE '%$givenname%' AND surename LIKE '%$surename%') OR email LIKE '%$email%'")
								->all();
							$a = 1;
							foreach ($user as $u) {
								$model->tempImport[$i][1][$a] = [
									"id"    => $u->id,
									"name"  => $u->name,
									"email" => $u->email,
								];
								$a++;
							}
//Just make sure it is int
							$model->tempImport[$i][4][0] = (int)$model->tempImport[$i][4][0];
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

	public function actionPopup($id, $round_id)
	{
		$model = $this->findModel($id);

		return $this->renderAjax("_popup", [
			"model"    => $model,
			"round_id" => $round_id
		]);
	}

	public function actionWatch($id)
	{
		$model = $this->findModel($id);

		if ($model->are_watched == 0)
			$model->are_watched = 1;
		else {
			$model->are_watched = 0;
		}

		if (!$model->save()) {
			Yii::$app->session->addFlash("error", $model->getErrors("are_watched"));
		}

		$model->refresh();
		if (Yii::$app->request->isAjax) {
			$this->runAction("index");
		} else
			return $this->redirect(['adjudicator/index', 'tournament_id' => $this->_tournament->id]);
	}

	public function actionBreak($id)
	{
		$model = $this->findModel($id);

		if ($model->breaking == 0)
			$model->breaking = 1;
		else {
			$model->breaking = 0;
		}

		if (!$model->save()) {
			Yii::$app->session->addFlash("error", $model->getErrors("breaking"));
		}

		$model->refresh();
		if (Yii::$app->request->isAjax) {
			unset($_GET["id"]);
			$this->runAction("index");
		} else
			return $this->redirect(['adjudicator/index', 'tournament_id' => $this->_tournament->id]);
	}

	public function actionResetwatched()
	{
		$adju = Adjudicator::updateAll(["are_watched" => 0], ["tournament_id" => $this->_tournament->id]);
		//Yii::$app->session->addFlash("info", $adju . " Adjudicators reseted");

		if (Yii::$app->request->isAjax) {
			$this->runAction("index");
		} else
			return $this->redirect(['adjudicator/index', 'tournament_id' => $this->_tournament->id]);
	}

	/**
	 * Returns 20 Adjudicators in an JSON List
	 *
	 * @param type $search
	 * @param type $id
	 */
	public function actionList(array $search = null, $id = null, $tournament_id)
	{
		$out = ['more' => false];
		if (!is_null($search["term"]) && $search["term"] != "") {
			$query = new \yii\db\Query;
			$query->select(["adjudicator.id", "CONCAT(user.givenname, ' ', user.surename) as text"])
				->from('adjudicator')
				->leftJoin("user", "adjudicator.user_id = user.id")
				->where('tournament_id = "' . $tournament_id . '" AND CONCAT(user.givenname, " ", user.surename) LIKE "%' . $search["term"] . '%"')
				->limit(20);
			$command = $query->createCommand();
			$data = $command->queryAll();
			$out['results'] = array_values($data);
		} elseif ($id > 0) {
			$out['results'] = ['id' => $id, 'text' => Adjudicator::findOne($id)->name];
		} else {
			$out['results'] = ['id' => 0, 'text' => Yii::t("app", 'No matching records found')];
		}
		echo \yii\helpers\Json::encode($out);
	}

}
