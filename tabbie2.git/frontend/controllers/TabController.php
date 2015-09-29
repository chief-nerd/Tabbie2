<?php

namespace frontend\controllers;

use common\components\filter\TournamentContextFilter;
use common\components\ObjectError;
use common\models\PublishTabSpeaker;
use common\models\PublishTabTeam;
use common\models\Tournament;
use Yii;
use yii\base\Exception;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * TabController implements the CRUD actions for DrawAfterRound model.
 */
class TabController extends BasetournamentController
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
						'actions' => ['live-team', 'live-speaker', 'outrounds', 'publish'],
						'matchCallback' => function ($rule, $action) {
							return ($this->_tournament->isTabMaster(Yii::$app->user->id));
						}
					],
				],
			],
		];
	}

	/**
	 * Lists all Team models.
	 *
	 * @return mixed
	 */
	public function actionLiveTeam()
	{
		$dataProvider = PublishTabTeam::getDataProvider($this->_tournament, true);

		return $this->render('team', [
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Lists all Team models.
	 *
	 * @return mixed
	 */
	public function actionLiveSpeaker()
	{
		$dataProvider = PublishTabSpeaker::getDataProvider($this->_tournament, true);

		return $this->render('speaker', [
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Lists all Team models.
	 *
	 * @return mixed
	 */
	public function actionOutrounds()
	{
		$model = $this->_tournament;

		return $this->render('outrounds', [
			'model' => $model
		]);
	}

	public function actionPublish()
	{
		$lines_team = PublishTabTeam::generateTeamTab($this->_tournament, true);

		foreach ($lines_team as $line) {
			$ptt = new PublishTabTeam([
				"tournament_id" => $this->_tournament->id,
				"team_id" => $line->object["id"],
				"enl_place" => $line->enl_place,
				"esl_place" => $line->esl_place,
				"cache_results" => json_encode($line->results_array),
				"speaks"    => $line->speaks,
			]);
			if (!$ptt->save())
				throw new Exception("Save Error " . ObjectError::getMsg($ptt));
		}

		$lines_speaker = PublishTabSpeaker::generateSpeakerTab($this->_tournament, true);

		foreach ($lines_speaker as $line) {
			if ($line->object) {
				$ptt = new PublishTabSpeaker([
					"tournament_id" => $this->_tournament->id,
					"user_id" => $line->object["speaker"]["id"],
					"enl_place" => $line->enl_place,
					"esl_place" => $line->esl_place,
					"cache_results" => json_encode($line->results_array),
					"speaks"    => $line->speaks,
				]);
				if (!$ptt->save())
					throw new Exception("Save Error " . ObjectError::getMsg($ptt));
			}
		}

		/** Close Tournament */

		$this->_tournament->status = Tournament::STATUS_CLOSED;
		$this->_tournament->save();
		Yii::$app->cache->delete("tournament_" . $this->_tournament->id);

		Yii::$app->session->addFlash("success", Yii::t("app", "Tab published and tournament closed. Go have a drink!"));

		return $this->redirect(["tournament/view", "id" => $this->_tournament->id]);
	}

}
