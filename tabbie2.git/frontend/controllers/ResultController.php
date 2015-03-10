<?php

namespace frontend\controllers;

use Yii;
use common\models\Result;
use common\models\search\ResultSearch;
use frontend\controllers\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\components\filter\TournamentContextFilter;
use yii\data\ActiveDataProvider;

/**
 * ResultController implements the CRUD actions for Result model.
 */
class ResultController extends BaseController {

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
     * Lists all Result models.
     * @return mixed
     */
    public function actionIndex() {

        $rounds = new ActiveDataProvider([
            'query' => \common\models\Round::findBySql("SELECT round.* from round "
                    . "LEFT JOIN debate ON round.id = debate.round_id "
                    . "LEFT JOIN result ON result.debate_id = debate.id "
                    . "WHERE round.tournament_id = " . $this->_tournament->id . " "
                    . "GROUP BY round.id")
        ]);

        return $this->render('index', [
                    'rounds' => $rounds,
        ]);
    }

    /**
     * Lists all Result models.
     * @return mixed
     */
    public function actionRound($id, $view = "venue") {
        $searchModel = new ResultSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->_tournament->id, $id);

        $publishpath = Yii::$app->assetManager->publish(Yii::getAlias("@frontend/assets/js/result_poll.js"));
        $this->view->registerJsFile($publishpath[1], [
            "depends" => [
                \yii\web\JqueryAsset::className(),
                \yii\widgets\PjaxAsset::className(),
            ],
        ]);

        $number = $dataProvider->getModels()[0]->round->number;

        switch ($view) {
            case "venue":
                $view = "venueview";
                break;
            case "table":
                $view = "tableview";
                break;
            default:
                throw new Exception("View does not exist", 404);
        }

        return $this->render($view, [
                    'round_id' => $id,
                    'round_number' => $number,
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Result model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Result model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id) {

        $result = Result::find()->where(["debate_id" => $id])->one();
        if (!$result instanceof Result) {
            $model = new Result();
            $model->debate_id = $id;

            if ($model->load(Yii::$app->request->post())) {
                if ($model->confirmed == "true") {
                    $adj = \common\models\Adjudicator::findOne(["user_id" => Yii::$app->user->id]);
                    $model->enteredBy_adjudicator_id = $adj->id;
                    if ($model->save())
                        return $this->render('thankyou', ["model" => $model]);
                    else
                        print_r($model->getErrors());
                } else {
                    $model->rankTeams();
                    return $this->render('confirm', [
                                'model' => $model,
                    ]);
                }
            }

            return $this->render('create', [
                        'model' => $model,
            ]);
        } else
            return $this->render('thankyou', ["model" => $result]);
    }

    /**
     * Updates an existing Result model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Result model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Result model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Result the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Result::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionManual() {

        $model = new Result();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->confirmed == "true") {
                $adj = \common\models\Adjudicator::findOne(["user_id" => Yii::$app->user->id]);
                $model->enteredBy_adjudicator_id = $adj->id;
                if ($model->save())
                    return $this->render('thankyou', ["model" => $model]);
                else {
                    print_r($model->getErrors());
                }
            } else {
                $model->rankTeams();
                return $this->render('confirm', [
                            'model' => $model,
                ]);
            }
        }

        return $this->render('manual', [
                    'model' => $model,
        ]);
    }

}
