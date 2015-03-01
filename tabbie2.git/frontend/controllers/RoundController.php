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
use mPDF;

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
        $debateDataProvider = $debateSearchModel->search(Yii::$app->request->queryParams, $this->_tournament->id, $id);

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
        $model->number = $this->nextRoundNumber();
        $model->tournament_id = $this->_tournament->id;

        if ($model->load(Yii::$app->request->post())) {

            if ($model->save() && $model->generateDraw()) {
                return $this->redirect(['view', 'id' => $model->id, "tournament_id" => $model->tournament_id]);
            } else {
                Yii::$app->session->addFlash("error", print_r($model->getErrors(), true));
            }
        }

        return $this->render('create', [
                    'model' => $model,
        ]);
    }

    public function nextRoundNumber() {
        $lastRound = Round::find()->where(["tournament_id" => $this->_tournament->id])->orderBy(["number" => SORT_DESC])->one();
        if (!$lastRound)
            return 1;
        else
            return ($lastRound->number + 1);
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

    public function actionRedraw($id) {
        $model = Round::findOne(["id" => $id]);

        if ($model instanceof Round) {
            foreach ($model->debates as $d) {
                $d->delete();
            }
            if (!$model->generateDraw()) {
                Yii::$app->session->addFlash("error", print_r($model->getErrors(), true));
            }
        }

        return $this->redirect(['view', 'id' => $model->id, "tournament_id" => $model->tournament_id]);
    }

    public function actionPrintballots($id, $debug = false) {
        $model = Round::findOne(["id" => $id]);

        $title = 'Ballots Round #' . $model->id . ' of the ' . $model->tournament->fullname;

        $mpdf = new mPDF("", "A4-L");
        $mpdf->SetDrawColor(255, 0, 0);

        $stylesheet = file_get_contents(Yii::getAlias('@frontend/assets/css/ballot.css'));
        $mpdf->WriteHTML($stylesheet, 1);

        foreach ($model->debates as $debate) {
            $mpdf->AddPage();
            $content = $this->renderPartial('_ballotTemplate', [
                'tournament' => $model->tournament,
                'round' => $model,
                'debate' => $debate,
            ]);
            $mpdf->WriteHTML($content);

            $offset = 121;
            $space = 32;
            $height = 8;

            $mpdf->Rect(80, ($offset), 20, $height, 'D');
            $mpdf->Rect(80, ($offset + 8), 20, $height, 'D');
            $mpdf->Rect(103, ($offset), 20, $height * 2, 'D');

            $mpdf->Rect(210, ($offset), 20, $height, 'D');
            $mpdf->Rect(210, ($offset + $height), 20, $height, 'D');
            $mpdf->Rect(233, ($offset), 20, $height * 2, 'D');

            $mpdf->Rect(210, ($offset + $space), 20, $height, 'D');
            $mpdf->Rect(210, ($offset + $space + $height), 20, $height, 'D');
            $mpdf->Rect(233, ($offset + $space), 20, $height * 2, 'D');

            $mpdf->Rect(80, ($offset + $space), 20, $height, 'D');
            $mpdf->Rect(80, ($offset + $space + $height), 20, $height, 'D');
            $mpdf->Rect(103, ($offset + $space), 20, $height * 2, 'D');
        }
        $mpdf->SetTitle($title);
        $mpdf->Output();
        exit;
    }

}
