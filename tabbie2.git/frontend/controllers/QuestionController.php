<?php

namespace frontend\controllers;

use Yii;
use common\models\Question;
use common\models\search\QuestionSearch;
use frontend\controllers\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\components\filter\TournamentContextFilter;

/**
 * QuestionController implements the CRUD actions for question model.
 */
class QuestionController extends BaseController {

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
     * Lists all question models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new QuestionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->_tournament->id);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single question model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new question model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Question();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                $ThasQ = new \common\models\TournamentHasQuestion();
                $ThasQ->tournament_id = $this->_tournament->id;
                $ThasQ->questions_id = $model->id;
                if ($ThasQ->save())
                    return $this->redirect(['view', 'id' => $model->id, "tournament_id" => $this->_tournament->id]);
                else
                    $model->addError("id", "Can't save Tournament Connection");
            }
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing question model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id, "tournament_id" => $this->_tournament->id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing question model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        /* @var $model Question */
        $model = $this->findModel($id);
        if ($model) {
            if ($model->tournamentHasQuestion[0]->delete())
                if ($model->delete())
                    Yii::$app->session->addFlash("success", "Question deleted");
        }
        else {
            Yii::$app->session->addFlash("error", "Can't delete Question");
        }
        return $this->redirect(['index', "tournament_id" => $this->_tournament->id]);
    }

    /**
     * Finds the question model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return question the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = question::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
