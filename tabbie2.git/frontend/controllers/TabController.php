<?php

namespace frontend\controllers;

use common\components\filter\TournamentContextFilter;
use common\models\TabAfterRound;
use common\models\User;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;

/**
 * TabController implements the CRUD actions for DrawAfterRound model.
 */
class TabController extends BaseController {

	public function behaviors() {
		return [
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
	public function actionTeam() {

		$results = \common\models\Result::find()->leftJoin("debate", "debate.id = result.debate_id")->where([
			"debate.tournament_id" => $this->_tournament->id,
		])->all();

		$teams = \common\models\Team::find()->where(["tournament_id" => $this->_tournament->id])->all();

		$lines = [];

		foreach ($teams as $team) {
			$lines[$team->id] = new \common\models\TabLine([
				"object" => $team,
				"points" => 0,
				"speaks" => 0,
			]);
		}

		foreach ($results as $result) {
			/* @var $result \common\models\Result */
			foreach (\common\models\Debate::positions() as $p) {
				$line = $lines[$result->debate->{$p . "_team_id"}];
				$line->points = $line->points + (4 - $result->{$p . "_place"});
				$line->results_array[$result->debate->round->number] = $result->{$p . "_place"};
				$line->speaks = $line->speaks + $result->{$p . "_A_speaks"} + $result->{$p . "_B_speaks"};
				$lines[$result->debate->{$p . "_team_id"}] = $line;
			}
		}

		usort($lines, "frontend\controllers\TabController::rankTeamsWithSpeaks");

		$i = 1;
		$jumpover = 0;
		foreach ($lines as $index => $line) {
			if (isset($lines[$index - 1]))
				if (!($lines[$index - 1]->points == $lines[$index]->points && $lines[$index - 1]->speaks == $lines[$index]->speaks)) {
					$i++;
					if ($jumpover > 0) {
						$i = $i + $jumpover;
						$jumpover = 0;
					}
				}
				else {
					$jumpover++;
				}
			$line->enl_place = $i;
			$lines[$index] = $line;
		}

		if ($this->_tournament->has_esl) {
			$i = 0;
			$jumpover = 0;
			foreach ($lines as $index => $line) {
				if ($line->object->language_status >= User::LANGUAGE_ESL) {
					if (isset($lines[$index - 1])) {
						if (!($lines[$index - 1]->points == $lines[$index]->points && $lines[$index - 1]->speaks == $lines[$index]->speaks)) {
							$i++;
							if ($jumpover > 0) {
								$i = $i + $jumpover;
								$jumpover = 0;
							}
						}
						else {
							$jumpover++;
						}
					}
					else $i++;

					$line->esl_place = $i;
					$lines[$index] = $line;
				}
			}
		}

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
	public function actionSpeaker() {

		$results = \common\models\Result::find()->leftJoin("debate", "debate.id = result.debate_id")->where([
			"debate.tournament_id" => $this->_tournament->id,
		])->all();

		$teams = \common\models\Team::find()->where(["tournament_id" => $this->_tournament->id])->all();

		$lines = [];

		foreach ($teams as $team) {
			/** @var \common\models\Team $team */
			$lines[$team->speakerA_id] = new \common\models\TabLine([
				"object" => $team->speakerA,
				"points" => 0,
				"speaks" => 0,
			]);
			$lines[$team->speakerB_id] = new \common\models\TabLine([
				"object" => $team->speakerB,
				"points" => 0,
				"speaks" => 0,
			]);
		}

		foreach ($results as $result) {
			/* @var $result \common\models\Result */
			foreach (\common\models\Debate::positions() as $p) {

				$line = $lines[$result->debate->{$p . "_team"}->speakerA_id];
				$line->points = $line->points + (4 - $result->{$p . "_place"});
				$line->results_array[$result->debate->round->number] = $result->{$p . "_A_speaks"};
				$line->speaks = $line->speaks + $result->{$p . "_A_speaks"};
				$lines[$result->debate->{$p . "_team"}->speakerA_id] = $line;

				$line = $lines[$result->debate->{$p . "_team"}->speakerB_id];
				$line->points = $line->points + (4 - $result->{$p . "_place"});
				$line->results_array[$result->debate->round->number] = $result->{$p . "_B_speaks"};
				$line->speaks = $line->speaks + $result->{$p . "_B_speaks"};
				$lines[$result->debate->{$p . "_team"}->speakerB_id] = $line;
			}
		}

		usort($lines, "frontend\controllers\TabController::rankSpeaker");

		$i = 1;
		$jumpover = 0;
		foreach ($lines as $index => $line) {
			if (isset($lines[$index - 1]))
				if (!($lines[$index - 1]->points == $lines[$index]->points && $lines[$index - 1]->speaks == $lines[$index]->speaks)) {
					$i++;
					if ($jumpover > 0) {
						$i = $i + $jumpover;
						$jumpover = 0;
					}
				}
				else {
					$jumpover++;
				}
			$line->enl_place = $i;
			$lines[$index] = $line;
		}

		if ($this->_tournament->has_esl) {
			$i = 0;
			$jumpover = 0;
			foreach ($lines as $index => $line) {
				if ($line->object->language_status >= User::LANGUAGE_ESL) {
					if ($line->object->id) {
						if (isset($lines[$index - 1])) {
							if (!($lines[$index - 1]->points == $lines[$index]->points && $lines[$index - 1]->speaks == $lines[$index]->speaks)) {
								$i++;
								if ($jumpover > 0) {
									$i = $i + $jumpover;
									$jumpover = 0;
								}
							}
							else {
								$jumpover++;
							}
						}
						else $i++;

						$line->esl_place = $i;
						$lines[$index] = $line;
					}
				}
			}
		}

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

	/**
	 * Rank Teams by points and then by Speaker points
	 *
	 * @param $a
	 * @param $b
	 *
	 * @using rankSpeaker
	 * @return int
	 */
	public function rankTeamsWithSpeaks($a, $b) {
		$ap = $a["points"];
		$as = $a["speaks"];
		$bp = $b["points"];
		$bs = $b["speaks"];
		return ($ap < $bp) ? 1 : (($ap > $bp) ? -1 : $this->rankSpeaker($a, $b));
	}

	/**
	 * Rank by Speaker Points
	 *
	 * @param $a
	 * @param $b
	 *
	 * @return int
	 */
	public function rankSpeaker($a, $b) {
		$as = $a["speaks"];
		$bs = $b["speaks"];
		return (($as < $bs) ? 1 : (($as > $bs) ? -1 : 0));
	}

}
