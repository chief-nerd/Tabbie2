<?php

namespace frontend\controllers;

use Yii;
use common\models\Venue;
use common\models\Tournament;
use yii\data\ActiveDataProvider;
use frontend\controllers\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\components\filter\TournamentContextFilter;

/**
 * VenueController implements the CRUD actions for Venue model.
 */
class VenueController extends BaseController {

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
     * Lists all Venue models.
     * @return mixed
     */
    public function actionIndex() {
        $dataProvider = new ActiveDataProvider([
            'query' => Venue::find($this->_tournament->id),
        ]);

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
                    'tournament' => $this->_tournament,
        ]);
    }

    /**
     * Displays a single Venue model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Venue model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Venue();
        $model->tournament_id = $this->_tournament->id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view',
                        'id' => $model->id,
                        'tournament_id' => $this->_tournament->id
            ]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Venue model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id, 'tournament_id' => $this->_tournament->id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Venue model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['tournament/view', 'id' => $this->_tournament->id]);
    }

    /**
     * Finds the Venue model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Venue the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Venue::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
