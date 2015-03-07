<?php

namespace frontend\controllers;

use Yii;
use common\models\Team;
use common\models\search\TeamSearch;
use frontend\controllers\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\components\filter\TournamentContextFilter;

/**
 * TeamController implements the CRUD actions for Team model.
 */
class TeamController extends BaseController {

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
    public function actionIndex() {
        $searchModel = new TeamSearch(["tournament_id" => $this->_tournament->id]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Team model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Team model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Team();
        $model->tournament_id = $this->_tournament->id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id, 'tournament_id' => $model->tournament_id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Team model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'tournament_id' => $model->tournament_id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Team model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['tournament/view', 'id' => $this->_tournament->id]);
    }

    public function actionImport() {
        $tournament = $this->_tournament;
        $model = new \frontend\models\ImportForm();

        if (Yii::$app->request->isPost) {
            $model->scenario = "screen";
            if (Yii::$app->request->post("makeItSo", false)) { //Everything corrected
                $choices = Yii::$app->request->post("field", false);
                $model->tempImport = unserialize(Yii::$app->request->post("csvFile", false));

                //APPLY CHOICES
                if (is_array($choices)) {
                    foreach ($choices as $row => $choice) {
                        foreach ($choice as $id => $value) {
                            $input = $model->tempImport[$row][$id][0];
                            unset($model->tempImport[$row][$id]);
                            $model->tempImport[$row][$id][0] = $input;
                            $model->tempImport[$row][$id][1]["id"] = $value;
                        }
                    }
                }

                //INSERT DATA
                for ($r = 1; $r <= count($model->tempImport); $r++) {
                    $row = $model->tempImport[$r];

                    //Society
                    if (count($row[1]) == 1) { //NEW
                        $society = new \common\models\Society();
                        $society->fullname = $row[1][0];
                        $society->abr = \common\models\Society::generateAbr($society->fullname);
                        $society->save();
                        $societyID = $society->id;
                    } else if (count($row[1]) == 2) {
                        $societyID = $row[1][1]["id"];
                    }

                    //UserA
                    if (count($row[2]) == 1) { //NEW
                        $userA = new \common\models\User();
                        $userA->givenname = $row[2][0];
                        $userA->surename = $row[3][0];
                        $userA->username = $userA->givenname . $userA->surename;
                        $userA->email = $row[4][0];
                        $userA->setPassword($userA->email);
                        $userA->generateAuthKey();
                        $userA->time = $userA->last_change = date("Y-m-d H:i:s");
                        if (!$userA->save()) {
                            $inSociety = new \common\models\InSociety();
                            $inSociety->user_id = $userA->id;
                            $inSociety->society_id = $societyID;
                            $inSociety->starting = date("Y-m-d H:i:s");
                            $inSociety->save();
                            Yii::$app->session->addFlash("error", "Save error: " . print_r($userA->getErrors(), true));
                        }
                        $userAID = $userA->id;
                    } else if (count($row[2]) == 2) {
                        $userAID = $row[2][1]["id"];
                    }

                    //UserB
                    if (count($row[5]) == 1) { //NEW
                        $userB = new \common\models\User();
                        $userB->givenname = $row[5][0];
                        $userB->surename = $row[6][0];
                        $userB->username = $userB->givenname . $userB->surename;
                        $userB->email = $row[7][0];
                        $userB->setPassword($userB->email);
                        $userB->generateAuthKey();
                        $userB->time = $userB->last_change = date("Y-m-d H:i:s");
                        if (!$userB->save()) {
                            $inSociety = new \common\models\InSociety();
                            $inSociety->user_id = $userB->id;
                            $inSociety->society_id = $societyID;
                            $inSociety->starting = date("Y-m-d H:i:s");
                            $inSociety->save();
                            Yii::$app->session->addFlash("error", "Save error: " . print_r($userB->getErrors(), true));
                        }
                        $userBID = $userB->id;
                    } else if (count($row[5]) == 2) {
                        $userBID = $row[5][1]["id"];
                    }

                    $team = new Team();
                    $team->name = $row[0][0];
                    $team->tournament_id = $this->_tournament->id;
                    $team->speakerA_id = $userAID;
                    $team->speakerB_id = $userBID;
                    $team->society_id = $societyID;
                    if (!$team->save())
                        Yii::$app->session->addFlash("error", "Save error: " . print_r($team->getErrors(), true));
                }
                return $this->redirect(['index', "tournament_id" => $this->_tournament->id]);
            } else { //FORM UPLOAD
                $file = \yii\web\UploadedFile::getInstance($model, 'csvFile');
                $model->load(Yii::$app->request->post());

                $row = 0;
                if ($file && ($handle = fopen($file->tempName, "r")) !== FALSE) {
                    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {

                        if ($row == 0) { //Don't use first column
                            $row++;
                            continue;
                        }

                        if (($num = count($data)) != 8) {
                            throw new \yii\base\Exception("500", "File Syntax Wrong");
                        }
                        for ($c = 0; $c < $num; $c++) {
                            $model->tempImport[$row][$c][0] = trim($data[$c]);
                        }
                        $row++;
                    }
                    fclose($handle);

                    //Find Matches
                    for ($i = 1; $i <= count($model->tempImport); $i++) {
                        //TeamName - not match
                        //
                        //Debating Society
                        $name = $model->tempImport[$i][1][0];
                        $societies = \common\models\Society::find()->where("fullname LIKE '%$name%'")->all();
                        $model->tempImport[$i][1] = array();
                        $model->tempImport[$i][1][0] = $name;
                        $a = 1;
                        foreach ($societies as $s) {
                            $model->tempImport[$i][1][$a] = [
                                "id" => $s->id,
                                "name" => $s->fullname,
                            ];
                            $a++;
                        }

                        //User A
                        $givenname = $model->tempImport[$i][2][0];
                        $surename = $model->tempImport[$i][3][0];
                        $email = $model->tempImport[$i][4][0];
                        $user = \common\models\User::find()->where("(givenname LIKE '%$givenname%' AND surename LIKE '%$surename%') OR surename LIKE '%$email%'")->all();
                        $a = 1;
                        foreach ($user as $u) {
                            $model->tempImport[$i][2][$a] = [
                                "id" => $u->id,
                                "name" => $u->name,
                                "email" => $u->email,
                            ];
                            $a++;
                        }

                        //User B
                        $givenname = $model->tempImport[$i][5][0];
                        $surename = $model->tempImport[$i][6][0];
                        $email = $model->tempImport[$i][7][0];
                        $user = \common\models\User::find()->where("(givenname LIKE '%$givenname%' AND surename LIKE '%$surename%') OR surename LIKE '%$email%'")->all();
                        $a = 1;
                        foreach ($user as $u) {
                            $model->tempImport[$i][5][$a] = [
                                "id" => $u->id,
                                "name" => $u->name,
                                "email" => $u->email,
                            ];
                            $a++;
                        }
                    }
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

    /**
     * Finds the Team model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Team the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Team::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
