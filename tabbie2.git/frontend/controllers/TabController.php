<?php

namespace frontend\controllers;

use Yii;
use common\models\TabAfterRound;
use common\models\search\TabTeamSearch;
use frontend\controllers\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \common\components\filter\TournamentContextFilter;
use yii\data\ArrayDataProvider;

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
                "team" => $team,
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
                } else {
                    $jumpover++;
                }
            $line->enl_place = $i;
            $lines[$index] = $line;
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

    public function rankTeamsWithSpeaks($a, $b) {
        $ap = $a["points"];
        $as = $a["speaks"];
        $bp = $b["points"];
        $bs = $b["speaks"];
        return ($ap < $bp) ? 1 : (($ap > $bp) ? -1 : (($as < $bs) ? 1 : (($as > $bs) ? -1 : 0)));
    }

}
