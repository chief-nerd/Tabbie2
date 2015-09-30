<?php

namespace frontend\controllers;

use common\components\filter\TournamentContextFilter;
use common\components\ObjectError;
use common\models\Adjudicator;
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
class StatsController extends BasetournamentController
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
						'actions'       => ['motion', 'speaks', 'team-tab', 'speaker-tab', 'breaking-adjudicators', 'outrounds'],
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
		$dataProvider = PublishTabTeam::getDataProvider($model);

		$html = $this->renderPartial("tab_team", compact("model", "dataProvider"));

		return Json::encode($html);
	}

	public function actionSpeakerTab()
	{
		$model = $this->_tournament;
		$dataProvider = PublishTabSpeaker::getDataProvider($model);

		$html = $this->renderPartial("tab_speaker", compact("model", "dataProvider"));

		return Json::encode($html);
	}

	public function actionOutrounds() {
		$model = $this->_tournament;
		$html = $this->renderPartial("outrounds", compact("model"));

		return Json::encode($html);
	}

	public function actionBreakingAdjudicators()
	{
		$model = $this->_tournament;

		$adjudicators = Adjudicator::find()
			->tournament($this->_tournament->id)
			->joinWith("user")
			->andWhere(["breaking" => 1])->orderBy(["surename" => SORT_ASC])
			->all();
		$html = $this->renderPartial("breaking_adjudicators", compact("adjudicators"));

		return Json::encode($html);
	}

}
