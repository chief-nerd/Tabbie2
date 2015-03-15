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
use yii\filters\AccessControl;

/**
 * VenueController implements the CRUD actions for Venue model.
 * @property Tournament $_tournament
 */
class VenueController extends BaseController {

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'matchCallback' => function ($rule, $action) {
                    return (Yii::$app->user->isTabMaster($this->_tournament) || Yii::$app->user->isConvenor($this->_tournament));
                }
                    ],
	                [
		                'allow' => true,
		                'actions' => ['create', 'update', 'delete', 'active', 'import'],
		                'matchCallback' => function ($rule, $action) {
			                return (Yii::$app->user->isTabMaster($this->_tournament));
		                }
	                ],
                ],
            ],
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
            'query' => Venue::find()->where(["tournament_id" => $this->_tournament->id]),
        ]);

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
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
        $model->active = true;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id, 'tournament_id' => $model->tournament_id
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
            return $this->redirect(['view', 'id' => $model->id, 'tournament_id' => $model->tournament_id]);
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
     * Toggle a Venue visability
     * @param integer $id
     * @return mixed
     */
    public function actionActive($id) {
        $model = $this->findModel($id);

        if ($model->active == 0)
            $model->active = 1;
        else {
            $model->active = 0;
        }

        if (!$model->save()) {
            Yii::$app->session->addFlash("error", $model->getErrors("active"));
        }

        return $this->redirect(['venue/index', 'tournament_id' => $this->_tournament->id]);
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

    public function actionImport() {
        set_time_limit(0);
        $tournament = $this->_tournament;
        $model = new \frontend\models\ImportForm();

        if (Yii::$app->request->isPost) {
            $model->scenario = "screen";

            if (Yii::$app->request->post("makeItSo", false)) { //Everything corrected
                $model->tempImport = unserialize(Yii::$app->request->post("csvFile", false));
//INSERT DATA
                for ($r = 1; $r <= count($model->tempImport); $r++) {
                    $row = $model->tempImport[$r];

                    $venue = new Venue();
                    $venue->name = $row[0];
                    $venue->active = $row[1];
                    $venue->tournament_id = $this->_tournament->id;
                    $venue->save();
                }
                return $this->redirect(['index', "tournament_id" => $this->_tournament->id]);
            } else { //FORM UPLOAD
                $file = \yii\web\UploadedFile::getInstance($model, 'csvFile');
                $model->load(Yii::$app->request->post());

                $row = 0;
                if ($file && ($handle = fopen($file->tempName, "r")) !== FALSE) {
                    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {

                        if ($row == 0) { //Don't use first row
                            $row++;
                            continue;
                        }

                        if (($num = count($data)) != 2) {
                            throw new \yii\base\Exception("500", "File Syntax Wrong");
                        }

                        for ($c = 0; $c < $num; $c++) {
                            $model->tempImport[$row][$c] = trim($data[$c]);
                        }
                        $row++;
                    }
                    fclose($handle);
                } else {
                    Yii::$app->session->addFlash("error", "No File available");
                    print_r($file);
                }
            }
        } else
            $model->scenario = "upload";

        return $this->render('import', [
                    "model" => $model,
                    "tournament" => $tournament
        ]);
    }

}
