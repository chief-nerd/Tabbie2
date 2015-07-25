<?php

namespace frontend\controllers;

use common\components\filter\TournamentContextFilter;
use common\models\LanguageOfficer;
use common\models\search\UserSearch;
use common\models\Team;
use common\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * Site controller
 */
class LanguageController extends BaseTournamentController
{

	/**
	 * @inheritdoc
	 */
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
						'actions' => ['index', 'set', 'officer', 'officer-add', 'officer-delete'],
						'matchCallback' => function ($rule, $action) {
							return ($this->_tournament->isLanguageOfficer(Yii::$app->user->id) || $this->_tournament->isTabMaster(Yii::$app->user->id));
						}
					],
				],
			],
			'verbs'  => [
				'class' => VerbFilter::className(),
				'actions' => [
					'logout' => ['post'],
				],
			],
		];
	}

	public function actionIndex()
	{
		$searchModel = new UserSearch();
		$dataProvider = $searchModel->searchTournament(Yii::$app->request->queryParams, $this->_tournament->id);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Sets the Users Language Level
	 * ONLY by Language Officer!
	 *
	 * @param integer $userid
	 * @param string  $status
	 *
	 * @return \yii\web\Response
	 */
	public function actionSet($userid, $status)
	{

		$user = User::findOne($userid);
		if ($user instanceof User) {
			switch ($status) {
				case "ENL":
					$user->language_status = User::LANGUAGE_ENL;
					break;
				case "ESL":
					$user->language_status = User::LANGUAGE_ESL;
					break;
				case "EFL":
					$user->language_status = User::LANGUAGE_EFL;
					break;
				default:
					Yii::$app->session->addFlash("error", Yii::t("app", "Not a valid Language Options in params"));
			}
			$user->language_status_by_id = Yii::$app->user->id;
			$user->language_status_update = date("Y-m-d H:i:s");

			if ($user->save()) {

				$teams = $user->getTeams()->where(["tournament_id" => $this->_tournament->id])->all();
				$addon = "";
				foreach ($teams as $team) {
					/** @var $team Team */
					if ($team->speakerA->language_status == $team->speakerB->language_status) {
						$team->language_status = $team->speakerA->language_status;
						$addon = Yii::t("app", " + Team upgraded to {status}", ["status" => User::getLanguageStatusLabel($team->language_status, true)]);
						$team->save();
					} else {
						$team->language_status = User::LANGUAGE_NONE;
						$team->save();
					}
				}

				Yii::$app->session->addFlash("success", Yii::t("app", "Language Settings saved") . $addon);
			} else
				Yii::$app->session->addFlash("error", Yii::t("app", "Error saving Language Settings"));
		} else
			Yii::$app->session->addFlash("error", Yii::t("app", "User not found!"));

		return $this->redirect(['language/index', "tournament_id" => $this->_tournament->id]);
	}


	public function actionOfficer()
	{

		$query = LanguageOfficer::find()->tournament($this->_tournament->id);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		return $this->render('officer', [
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionOfficerAdd()
	{
		$model = new LanguageOfficer();
		$model->tournament_id = $this->_tournament->id;

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			Yii::$app->session->addFlash("success", Yii::t("app", "Successfully added"));

			return $this->redirect(['language/officer', "tournament_id" => $model->tournament_id]);
		} else {
			return $this->render('officer_add', [
				'model' => $model,
			]);
		}

	}

	public function actionOfficerDelete($id)
	{
		$model = LanguageOfficer::find()->where([
			"tournament_id" => $this->_tournament->id,
			"user_id" => $id,
		])->one();

		if ($model && $model->delete())
			Yii::$app->session->addFlash("success", Yii::t("app", "Successfully deleted"));

		return $this->redirect(['language/officer', "tournament_id" => $model->tournament_id]);
	}
}
