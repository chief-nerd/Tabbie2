<?php

namespace frontend\controllers;

use Yii;
use common\models\search\DebateSearch;
use common\components\filter\TournamentContextFilter;
use yii\filters\VerbFilter;

/**
 * TournamentController implements the CRUD actions for Tournament model.
 */
class DisplayController extends BaseController {

    //Override Layout
    public $layout = "public";
    //MenuRight Items
    public $menuItems = [];

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

    public function actionIndex() {

        $rounds = \common\models\Round::find()->where([
                    "tournament_id" => $this->_tournament->id,
                    "published" => 1,
                    "displayed" => 0
                ])->all();
        $buttons = "";
        foreach ($rounds as $round) {
            $buttons .= \yii\helpers\Html::a("Show Round " . $round->id, [
                        "display/view",
                        "id" => $round->id,
                        "tournament_id" => $round->tournament_id], ["class" => "btn btn-lg btn-success"]);
        }

        if (Yii::$app->getRequest()->isAjax) {
            return $buttons;
        } else {

            $publishpath = Yii::$app->assetManager->publish(Yii::getAlias("@frontend/assets/js/roundPoll.js"));
            $this->view->registerJsFile($publishpath[1], [
                "depends" => [
                    \yii\web\JqueryAsset::className(),
                ],
            ]);

            return $this->render("index", [
                        "tournament" => $this->_tournament,
                        "already" => $buttons
            ]);
        }
    }

    public function actionView($id) {
        $round = \common\models\Round::findOne($id);
        $searchModel = new DebateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->_tournament->id, $id);

        $publishpath = Yii::$app->assetManager->publish(Yii::getAlias("@frontend/assets/js/autoScroll.js"));
        $this->view->registerJsFile($publishpath[1], [
            "depends" => [
                \yii\web\JqueryAsset::className(),
            ],
        ]);

        return $this->render("view", [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'round' => $round,
        ]);
    }

    public function actionStart($id) {
        $round = \common\models\Round::findOne($id);
        if ($round instanceof \common\models\Round) {
            $round->displayed = 1;
            $round->prep_started = date("Y-m-d H:i:s");
            if ($round->save())
                return "1";
        }
        return "0";
    }

}
