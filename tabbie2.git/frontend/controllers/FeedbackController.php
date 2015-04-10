<?php

namespace frontend\controllers;

use common\components\filter\TournamentContextFilter;
use common\models\AdjudicatorInPanel;
use common\models\Answer;
use common\models\Debate;
use common\models\feedback;
use common\models\FeedbackHasAnswer;
use common\models\search\AnswerSearch;
use common\models\search\FeedbackSearch;
use common\models\Team;
use Yii;
use yii\base\Exception;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * FeedbackController implements the CRUD actions for feedback model.
 */
class FeedbackController extends BaseTournamentController {

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
						'actions' => ['create'],
						'matchCallback' => function ($rule, $action) {
							/** @var Debate $debate */
							$ref = Yii::$app->user->hasOpenFeedback($this->_tournament);
							if (is_array($ref) && $ref["id"] == Yii::$app->request->get("id")) {
								return true;
							}
							return false;
						},
					],
					[
						'allow' => true,
						'actions' => ['index', 'view', 'create', 'adjudicator'],
						'matchCallback' => function ($rule, $action) {
							return (Yii::$app->user->isTabMaster($this->_tournament));
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
	 * Lists all feedback models.
	 *
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new FeedbackSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

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
	public function actionView($id) {
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	/**
	 * Creates a new feedback model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate($id, $type, $ref) {

		$already_entered = false;

		$feedback = new Feedback();
		$feedback->debate_id = $id;
		$feedback->time = date("Y-m-d H:i:s");

		switch ($type) {
			case Feedback::FROM_CHAIR:
			case Feedback::FROM_WING:
				$object = AdjudicatorInPanel::findOne(["adjudicator_id" => $ref, "panel_id" => $feedback->debate->panel_id]);
				if (!$object) {
					throw new Exception("Chair in Panel not found - type wrong?");
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
					throw new Exception("Team not found - type wrong?");
				}

				foreach ($object->getTeams(true) as $pos => $team_id) {
					if ($team_id == $ref) {
						$already_entered = $object->{$pos . "_feedback"};
						$team_pos = $pos;
					}
				}
				break;
			default:
				throw new Exception("No type");
		}

		foreach ($this->_tournament->getQuestions($type)->all() as $question) {
			$models[$question->id] = new Answer([
				"question_id" => $question->id,
			]);
		}

		if (Yii::$app->request->isPost && !$already_entered) {
			$allGood = true;
			$answers = Yii::$app->request->post("Answer");

			$feedback->save();

			foreach ($this->_tournament->getQuestions($type)->all() as $question) {
				$models[$question->id]->value = $answers[$question->id];
				$models[$question->id]->feedback_id = $feedback->id;

				if ($models[$question->id]->save()) {
						$allGood = false;
				}
				else {
					$allGood = false;
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
					throw new Exception("Save error " . print_r($object->getErrors(), true));
			}
			$already_entered = true;
		}

		if ($already_entered) {
			Yii::$app->session->addFlash("success", Yii::t("app", "Feedback successfully submitted"));
			return $this->redirect(['tournament/view', "id" => $this->_tournament->id]);
		}
		else
			return $this->render('create', ['models' => $models,]);
	}


	public function actionAdjudicator() {
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
	function actionDelete($id) {
		$this->findModel($id)->delete();

		return $this->redirect(['index']);
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
	function findModel($id) {
		if (($model = feedback::findOne($id)) !== null) {
			return $model;
		}
		else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

}
