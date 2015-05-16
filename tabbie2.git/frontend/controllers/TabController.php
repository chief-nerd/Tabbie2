<?php

namespace frontend\controllers;

use common\components\filter\TournamentContextFilter;
use common\models\PublishTabSpeaker;
use common\models\PublishTabTeam;
use common\models\TabAfterRound;
use common\models\Tournament;
use Yii;
use yii\base\Exception;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * TabController implements the CRUD actions for DrawAfterRound model.
 */
class TabController extends BaseTournamentController {

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
						'actions' => ['live-team', 'live-speaker', 'publish'],
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
	 * Lists all Team models.
	 *
	 * @return mixed
	 */
	public function actionLiveTeam() {

		$lines = PublishTabTeam::generateTeamTab($this->_tournament);

		$dataProvider = new ArrayDataProvider([
			'allModels' => $lines,
			'sort' => [
				'attributes' => ['enl_place'],
			],
			'pagination' => [
				'pageSize' => 99999,
			],
		]);

		return $this->render('team', [
			'lines' => $lines,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Lists all Team models.
	 *
	 * @return mixed
	 */
	public function actionLiveSpeaker() {

		$lines = PublishTabSpeaker::generateSpeakerTab($this->_tournament);

		$dataProvider = new ArrayDataProvider([
			'allModels' => $lines,
			'sort' => [
				'attributes' => ['enl_place'],
			],
			'pagination' => [
				'pageSize' => 99999,
			],
		]);

		return $this->render('speaker', [
			'lines' => $lines,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionPublish() {
		$lines_team = PublishTabTeam::generateTeamTab($this->_tournament);

		foreach ($lines_team as $line) {
			$ptt = new PublishTabTeam([
				"tournament_id" => $this->_tournament->id,
				"team_id" => $line->object->id,
				"enl_place" => $line->enl_place,
				"esl_place" => $line->esl_place,
				"cache_results" => json_encode($line->results_array),
				"speaks" => $line->speaks,
			]);
			if (!$ptt->save())
				throw new Exception("Save Error " . print_r($ptt->getErrors(), true));
		}

		$lines_speaker = PublishTabSpeaker::generateSpeakerTab($this->_tournament);

		foreach ($lines_speaker as $line) {
			if ($line->object) {
				$ptt = new PublishTabSpeaker([
					"tournament_id" => $this->_tournament->id,
					"user_id" => $line->object->id,
					"enl_place" => $line->enl_place,
					"esl_place" => $line->esl_place,
					"cache_results" => json_encode($line->results_array),
					"speaks" => $line->speaks,
				]);
				if (!$ptt->save())
					throw new Exception("Save Error " . print_r($ptt->getErrors(), true));
			}
		}

		/** Close Tournament */

		$this->_tournament->status = Tournament::STATUS_CLOSED;
		$this->_tournament->save();

		Yii::$app->session->addFlash("success", Yii::t("app", "Tab published and tournament closed. Go have a drink!"));

		return $this->redirect(["tournament/view", "id" => $this->_tournament->id]);
	}

}
