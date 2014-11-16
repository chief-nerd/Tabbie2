<?php

namespace frontend\controllers;

use Yii;
use common\models\Round;
use yii\data\ActiveDataProvider;
use frontend\controllers\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\components\filter\TournamentContextFilter;
use common\models\search\DebateSearch;

/**
 * RoundController implements the CRUD actions for Round model.
 */
class RoundController extends BaseController {

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
     * Lists all Round models.
     * @return mixed
     */
    public function actionIndex() {
        $dataProvider = new ActiveDataProvider([
            'query' => Round::find()->where(["tournament_id" => $this->_tournament->id]),
        ]);

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Round model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        $model = $this->findModel($id);
        $debateSearchModel = new DebateSearch();
        $debateDataProvider = $debateSearchModel->search(Yii::$app->request->queryParams);

        $publishpath = Yii::$app->assetManager->publish(Yii::getAlias("@frontend/assets/js/adjudicatorActions.js"));
        $this->view->registerJsFile($publishpath[1], [
            "depends" => [
                \yii\web\JqueryAsset::className(),
                \kartik\sortable\SortableAsset::className(),
                \yii\bootstrap\BootstrapAsset::className(),
                \yii\bootstrap\BootstrapPluginAsset::className()
            ],
            "data-href" => \yii\helpers\Url::to(["adjudicator/replace", "tournament_id" => $model->tournament_id]),
            "id" => "adjudicatorActionsJS",
        ]);

        return $this->render('view', [
                    'model' => $model,
                    'debateSearchModel' => $debateSearchModel,
                    'debateDataProvider' => $debateDataProvider,
        ]);
    }

    /**
     * Creates a new Round model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Round();
        $model->tournament_id = $this->_tournament->id;

        if ($model->load(Yii::$app->request->post())) {

            if ($model->generateDraw() && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id, "tournament_id" => $model->tournament_id]);
            } else {
                Yii::$app->session->addFlash("error", print_r($model->getErrors(), true));
            }
        } else {
            $model->id = $this->nextRoundNumber();
        }

        return $this->render('create', [
                    'model' => $model,
        ]);
    }

    public function nextRoundNumber() {
        $lastRound = Round::find()->where(["tournament_id" => $this->_tournament->id])->orderBy("id")->one();
        if (!$lastRound)
            return 1;
        else
            return ($lastRound->id + 1);
    }

    /**
     * Updates an existing Round model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id, "tournament_id" => $model->tournament_id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Round model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionPublish($id) {
        $model = $this->findModel($id);
        $model->published = 1;
        $model->save();

        return $this->redirect(['view', 'id' => $model->id, "tournament_id" => $model->tournament_id]);
    }

    /**
     * Finds the Round model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Round the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Round::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
