<?php

namespace frontend\controllers;

use Yii;
use common\models\Tournament;
use common\models\search\TournamentSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\components\filter\TournamentContextFilter;

/**
 * TournamentController implements the CRUD actions for Tournament model.
 */
class TournamentController extends BaseController {

    /**
     * Current Tournament Scope
     * Does not exist in index and create
     * @var Tournament
     */
    protected $_tournament;

    /**
     * Sets the Tournament Context
     * @param integer $id
     * @return boolean
     */
    public function setTournament($id) {
        $this->_tournament = $this->findModel($id);
        if ($this->_tournament instanceof Tournament)
            return true;
        else
            return false;
    }

    /**
     * Returns the current context
     * @return Tournament
     */
    public function getTournament() {
        return $this->_tournament;
    }

    public function behaviors() {
        return [
            'tournamentFilter' => [
                'class' => TournamentContextFilter::className(),
                'except' => ['index', 'create']
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
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
     * Lists all Tournament models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new TournamentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a current Tournament model.
     * @return mixed
     */
    public function actionView() {
        return $this->render('view', [
                    'model' => $this->findModel($this->_tournament->id),
        ]);
    }

    /**
     * Creates a new Tournament model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Tournament();

        if (Yii::$app->request->isPost) {
            $file = \yii\web\UploadedFile::getInstance($model, 'logo');
            $model->load(Yii::$app->request->post());
            $path = "/uploads/Tournament_" . str_replace(" ", "_", $model->fullname) . "." . $file->extension;
            if ($file->saveAs(Yii::getAlias("@frontend/web") . $path))
                $model->logo = $path;
            else
                $model->logo = null;

            if ($model->validate()) {
                $model->save();
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Tournament model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate() {
        $model = $this->findModel($this->_tournament->id);

        if (Yii::$app->request->isPost) {

            //Upload File
            $file = \yii\web\UploadedFile::getInstance($model, 'logo');

            //Save Old File Path
            $oldFile = $model->logo;
            //Load new values
            $model->load(Yii::$app->request->post());
            $path = "/uploads/Tournament_" . str_replace(" ", "_", $model->fullname) . ".jpg";

            if ($file !== NULL) {
                //Save new File
                if ($file->saveAs(Yii::getAlias("@frontend/web") . $path))
                    $model->logo = $path;
            } else
                $model->logo = $oldFile;

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
                    'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Tournament model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete() {
        $this->findModel($this->_tournament->id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Tournament model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Tournament the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Tournament::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
