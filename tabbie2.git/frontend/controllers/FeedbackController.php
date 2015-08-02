<?php

namespace frontend\controllers;

use common\components\filter\TournamentContextFilter;
use common\components\ObjectError;
use common\models\AdjudicatorInPanel;
use common\models\Answer;
use common\models\Debate;
use common\models\Feedback;
use common\models\search\AnswerSearch;
use common\models\search\FeedbackSearch;
use Yii;
use yii\base\Exception;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * FeedbackController implements the CRUD actions for feedback model.
 */
class FeedbackController extends BaseTournamentController
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
						'actions' => ['create'],
						'matchCallback' => function ($rule, $action) {
							/** @var Debate $debate */
							$info = $this->_tournament->getLastDebateInfo(Yii::$app->user->id);
							$ref = Yii::$app->user->hasOpenFeedback($info);
							if (is_array($ref) && $ref["id"] == Yii::$app->request->get("id")) {
								return true;
							}

							return false;
						},
					],
					[
						'allow'   => true,
						'actions' => ['index', 'view', 'create', 'adjudicator', 'tournament', 'tabbie'],
						'matchCallback' => function ($rule, $action) {
							return ($this->_tournament->isTabMaster(Yii::$app->user->id) ||
								$this->_tournament->isConvenor(Yii::$app->user->id) ||
								$this->_tournament->isCA(Yii::$app->user->id));
						}
					],
				],
			],
		];
	}

	/**
	 * Lists all feedback models.
	 *
	 * @return mixed
	 */
	public function actionIndex()
	{
		$searchModel = new FeedbackSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->_tournament->id);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single feedback model.
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
	 * Finds the feedback model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 *
	 * @return feedback the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected
	function findModel($id)
	{
		if (($model = feedback::findOne($id)) !== null) {
			return $model;
		} else
			throw new NotFoundHttpException('The requested page does not exist.');
	}

	/**
	 * Creates a new feedback model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate($id, $type, $ref)
	{
		$already_entered = false;

		$feedback = new Feedback();
		$feedback->debate_id = $id;
		$feedback->time = date("Y-m-d H:i:s");

		switch ($type) {
			case Feedback::FROM_CHAIR:
			case Feedback::FROM_WING:
				$object = AdjudicatorInPanel::findOne(["adjudicator_id" => $ref, "panel_id" => $feedback->debate->panel_id]);
				if (!$object) {
					throw new Exception(Yii::t("app", "Chair in Panel not found - type wrong?"));
				}
				$already_entered = $object->got_feedback;
				break;
			case Feedback::FROM_TEAM:
				$object = Debate::find()->tournament($this->_tournament->id)->andWhere(
					"og_team_id = :og OR oo_team_id = :oo OR cg_team_id = :cg OR co_team_id = :co",
					[
						"og" => $ref,
						"oo" => $ref,
						"cg" => $ref,
						"co" => $ref,
					]
				)->one();
				if (!$object) {
					throw new Exception(Yii::t("app", "Team not found - type wrong?"));
				}

				foreach ($object->getTeams(true) as $pos => $team_id) {
					if ($team_id == $ref) {
						$already_entered = $object->{$pos . "_feedback"};
						$team_pos = $pos;
					}
				}
				break;
			default:
				throw new Exception(Yii::t("app", "No type"));
		}

		$model_group = [];
		if ($type == Feedback::FROM_CHAIR) {
			foreach ($object->panel->getAdjudicators()->all() as $a) {
				if ($a->id != $ref) {
					$model_group[] = [
						"title" => $a->name,
						"item"  => $this->addQuestions($type),
						"id"    => $a->id,
					];
				}
			}
		} else {
			$model_group[] = [
				"title" => $object->panel->getChairInPanel()->adjudicator->name,
				"item"  => $this->addQuestions($type),
				"to"    => $object->panel->getChairInPanel()->adjudicator->id,
			];
		}

		if (Yii::$app->request->isPost && !$already_entered) {
			$allGood = true;
			$answers_group = Yii::$app->request->post("Answer");

			for ($group = 0; $group < count($answers_group); $group++) {

				if ($type == Feedback::FROM_CHAIR) {
					$toOption = Feedback::TO_WING;
				} else {
					$toOption = Feedback::TO_CHAIR;
				}

				$feedbackIterate = clone $feedback;
				$feedbackIterate->to_id = $model_group[$group]["to"];
				$feedbackIterate->to_type = $toOption;
				$feedbackIterate->save();

				$answers = $answers_group[$group];

				foreach ($this->_tournament->getQuestions($type)->all() as $question) {
					if (isset($answers[$question->id])) {
						if (is_array($answers[$question->id])) {
							$answer = json_encode($answers[$question->id]);
						} else
							$answer = $answers[$question->id];

						$model_group[$group]["item"][$question->id]->value = $answer;
						$model_group[$group]["item"][$question->id]->feedback_id = $feedbackIterate->id;
						$model_group[$group]["item"][$question->id]->question_id = $question->id;

						if ($model_group[$group]["item"][$question->id]->save()) {
							$allGood = true;
						} else {
							$allGood = false;
						}
					}
				}

				if ($allGood) {
					switch ($type) {
						case Feedback::FROM_CHAIR:
						case Feedback::FROM_WING:
							$object->got_feedback = 1;
							break;
						case Feedback::FROM_TEAM:
							$object->{$team_pos . "_feedback"} = 1;
							break;
					}

					if (!$object->save())
						throw new Exception("Save error " . ObjectError::getMsg($object));
				}
				$already_entered = true;
			}
		}

		if ($already_entered) {
			Yii::$app->session->addFlash("success", Yii::t("app", "Feedback successfully submitted"));

			return $this->redirect(['tournament/view', "id" => $this->_tournament->id]);
		} else {
			return $this->render('create', ['model_group' => $model_group]);
		}

	}

	public function addQuestions($type)
	{
		$models = [];
		foreach ($this->_tournament->getQuestions($type)->all() as $question) {
			$models[$question->id] = new Answer([
				"question_id" => $question->id,
			]);
		}

		return $models;
	}


	public function actionAdjudicator()
	{
		$searchModel = new AnswerSearch();
		$dataProvider = $searchModel->searchByAdjudicator(Yii::$app->request->queryParams);

		return $this->render('by_adjudicator', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);

	}

	/**
	 * Deletes an existing feedback model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public
	function actionDelete($id)
	{
		$this->findModel($id)->delete();

		return $this->redirect(['index']);

	}

	public function actionTournament()
	{
		return "Soon";
	}

	public function actionTabbie()
	{
		return "Soon";
	}

}
