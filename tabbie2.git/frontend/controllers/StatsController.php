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
use yii\helpers\Json;

/**
 * TabController implements the CRUD actions for DrawAfterRound model.
 */
class StatsController extends BaseTournamentController
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
						'actions'       => ['motion', 'speaks', 'team-tab', 'speaker-tab'],
						'matchCallback' => function ($rule, $action) {
							return ($this->_tournament->status >= Tournament::STATUS_CLOSED);
						}
					],
				],
			],
		];
	}

	public function actionMotion()
	{
		$model = $this->_tournament;
		$html = $this->renderPartial("motion", compact("model"));

		return Json::encode($html);
	}

	public function actionSpeaks()
	{
		$model = $this->_tournament;
		$html = $this->renderPartial("speaks_points", compact("model"));

		return Json::encode($html);
	}

	public function actionTeamTab()
	{
		$model = $this->_tournament;
		$html = $this->renderPartial("tab_team", compact("model"));

		return Json::encode($html);
	}

	public function actionSpeakerTab()
	{
		$model = $this->_tournament;
		$html = $this->renderPartial("tab_speaker", compact("model"));

		return Json::encode($html);
	}

}
