<?php

namespace frontend\controllers;

use Yii;
use common\models\Strikes;
use yii\data\ActiveDataProvider;
use frontend\controllers\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * StrikesController implements the CRUD actions for Strikes model.
 */
class StrikesController extends BaseController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Strikes models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Strikes::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Strikes model.
     * @param integer $team_id
     * @param integer $adjudicator_id
     * @return mixed
     */
    public function actionView($team_id, $adjudicator_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($team_id, $adjudicator_id),
        ]);
    }

    /**
     * Creates a new Strikes model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Strikes();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'team_id' => $model->team_id, 'adjudicator_id' => $model->adjudicator_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Strikes model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $team_id
     * @param integer $adjudicator_id
     * @return mixed
     */
    public function actionUpdate($team_id, $adjudicator_id)
    {
        $model = $this->findModel($team_id, $adjudicator_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'team_id' => $model->team_id, 'adjudicator_id' => $model->adjudicator_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Strikes model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $team_id
     * @param integer $adjudicator_id
     * @return mixed
     */
    public function actionDelete($team_id, $adjudicator_id)
    {
        $this->findModel($team_id, $adjudicator_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Strikes model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $team_id
     * @param integer $adjudicator_id
     * @return Strikes the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($team_id, $adjudicator_id)
    {
        if (($model = Strikes::findOne(['team_id' => $team_id, 'adjudicator_id' => $adjudicator_id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
