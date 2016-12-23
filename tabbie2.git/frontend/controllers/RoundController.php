<?php

namespace frontend\controllers;

use common\components\filter\TournamentContextFilter;
use common\components\ObjectError;
use common\models\Adjudicator;
use common\models\AdjudicatorInPanel;
use common\models\Debate;
use common\models\DrawLine;
use common\models\Panel;
use common\models\Result;
use common\models\Round;
use common\models\search\DebateSearch;
use common\models\Team;
use kartik\mpdf\Pdf;
use mPDF;
use Yii;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * RoundController implements the CRUD actions for Round model.
 */
class RoundController extends BasetournamentController
{

    public function behaviors()
    {
        return [
            'tournamentFilter' => [
                'class' => TournamentContextFilter::className(),
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'update', 'printballots', 'debatedetails', 'changevenue', 'switch-adjudicators'],
                        'matchCallback' => function ($rule, $action) {
                            return (
                                $this->_tournament->isTabMaster(Yii::$app->user->id) ||
                                $this->_tournament->isConvenor(Yii::$app->user->id) ||
                                $this->_tournament->isCA(Yii::$app->user->id)
                            );
                        }
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'delete', 'publish', 'redraw', 'improve', 'export', 'import'],
                        'matchCallback' => function ($rule, $action) {
                            return ($this->_tournament->isTabMaster(Yii::$app->user->id));
                        }
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Round models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Round::find()->where(["tournament_id" => $this->_tournament->id]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Round model.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $debateSearchModel = new DebateSearch();
        $debateDataProvider = $debateSearchModel->search(Yii::$app->request->queryParams, $this->_tournament->id, $id);

        // validate if there is a editable input saved via AJAX
        if (Yii::$app->request->post('hasEditable')) {
            // instantiate your debate model for saving
            $debateID = Yii::$app->request->post('editableKey');
            $debate = \common\models\Debate::findOne($debateID);

            // store a default json response as desired by editable
            $out = \yii\helpers\Json::encode(['output' => '', 'message' => '']);

            // return ajax json encoded response and exit

            return $out;
        }

        return $this->render('view', [
            'model' => $model,
            'debateSearchModel' => $debateSearchModel,
            'debateDataProvider' => $debateDataProvider,
        ]);
    }

    /**
     * Finds the Round model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Round the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Round::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Function called when move results are sent
     */
    public function actionChangevenue($id, $debateid)
    {
        $selected_debate = \common\models\Debate::findOne($debateid);

        if ($params = Yii::$app->request->get()) {

            $used_debate = \common\models\Debate::findOne(["venue_id" => $params["new_venue"], "round_id" => $selected_debate->round_id]);
            if ($used_debate instanceof \common\models\Debate) {
                $old_debate_venue = $selected_debate->venue_id;
                $selected_debate->venue_id = $used_debate->venue_id;
                $used_debate->venue_id = $old_debate_venue;
                if ($selected_debate->save() && $used_debate->save()) {
                    Yii::$app->session->setFlash('success', Yii::t("app", 'Venues switched'));
                } else {
                    Yii::$app->session->setFlash('error', Yii::t("app", 'Error while switching'));
                }
            } else {
                $selected_debate->venue_id = $params["new_venue"];
                if ($selected_debate->save()) {
                    Yii::$app->session->setFlash('success', Yii::t("app", 'New Venues set'));
                } else {
                    Yii::$app->session->setFlash('error', Yii::t("app", 'Error while setting new venue'));
                }
            }
        }

        return $this->redirect(["view", "id" => $id, "tournament_id" => $selected_debate->tournament_id, "view" => "#draw"]);
    }

    /**
     * Creates a new Round model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        if (\common\models\Team::find()->active()->tournament($this->_tournament->id)->count() % 4 != 0) {
            \Yii::$app->session->setFlash("error", Yii::t("app", "Can't create Round: Amount of Teams is not dividable by 4"));

            return $this->redirect(["team/index", "tournament_id" => $this->_tournament->id]);
        }

        $model = new Round();
        $model->tournament_id = $this->_tournament->id;
        $model->setNextRound();

        if ($model->type >= Round::TYP_OUT)
            $this->redirect(["outround/create", "tournament_id" => $this->_tournament->id]); //We are already on outround

        if ($model->load(Yii::$app->request->post())) {
            if (!$model->save() || !$model->generateWorkingDraw()) {
                Yii::$app->session->addFlash("error", ObjectError::getMsg($model));
            } else {
                $this->_tournament->updateAccessToken(500);
                return $this->redirect(['round/view', 'id' => $model->id, "tournament_id" => $model->tournament_id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Round model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
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
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        //Tag::deleteAll(["round_id" => $this->id]);

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Publish the Draw
     *
     * @param $id
     *
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionPublish($id)
    {
        $model = $this->findModel($id);
        $model->published = 1;
        if (!$model->save())
            Yii::$app->session->addFlash("error", ObjectError::getMsg($model));
        else
            Panel::updateAll(["used" => 1], ["tournament_id" => $this->_tournament->id]);

        return $this->redirect(['view', 'id' => $model->id, "tournament_id" => $model->tournament_id]);
    }

    public function actionRedraw($id)
    {
        $model = Round::findOne(["id" => $id]);

        if ($model instanceof Round) {

            $canProceed = true;
            foreach ($model->debates as $debate) {
                /** @var Debate $debate */
                if ($debate->result instanceof Result) {
                    $canProceed = false;
                }
            }

            if ($canProceed == false) {
                Yii::$app->session->addFlash("warning", Yii::t("app", "Already Results entered for this round. Can't redraw!"));
                $this->redirect(["view", "id" => $id, "tournament_id" => $this->_tournament->id]);
            }

            $time = microtime(true);

            foreach ($model->debates as $debate) {
                /** @var Debate $debate * */
                if (!$debate->panel->is_preset) { //Only delete non-preset panels
                    foreach ($debate->panel->adjudicatorInPanels as $aj)
                        $aj->delete();

                    $panelid = $debate->panel_id;
                    $debate->delete();
                    Panel::deleteAll(["id" => $panelid]);
                } else {
                    $debate->delete();
                }
            }

            if (!$model->generateWorkingDraw()) {
                Yii::$app->session->addFlash("error", ObjectError::getMsg($model));
            } else {
                $model->time = Yii::$app->time->UTC();
                $model->save();
                Yii::$app->session->addFlash("success", Yii::t("app", "Successfully redrawn in {secs}s", ["secs" => intval(microtime(true) - $time)]));
            }
        }

        //return $this->render("debug");
        return $this->redirect(['view', 'id' => $model->id, "tournament_id" => $model->tournament_id]);
    }

    public function actionImprove($id, $runs = null)
    {
        $model = Round::findOne(["id" => $id]);

        if ($model instanceof Round) {

            $canProceed = true;
            foreach ($model->debates as $debate) {
                /** @var Debate $debate */
                if ($debate->result instanceof Result) {
                    $canProceed = false;
                }
            }

            if ($canProceed == false) {
                Yii::$app->session->addFlash("warning", Yii::t("app", "Already Results entered for this round. Can't improve!"));
                $this->redirect(["view", "id" => $id, "tournament_id" => $this->_tournament->id]);
            }

            try {
                $time = microtime(true);
                $oldEnergy = $model->energy;
                $model->improveAdjudicator($runs);
                $model->save();
                $diff = ($oldEnergy - $model->energy);
                Yii::$app->session->addFlash(($diff > 0) ? "success" : "info", Yii::t("app", "Improved Energy by {diff} points in {secs}s", [
                    "diff" => $diff,
                    "secs" => intval(microtime(true) - $time),
                ]));

            } catch (Exception $ex) {
                Yii::$app->session->addFlash("error", $ex->getMessage());
            }
        }

        //return $this->render("debug");
        return $this->redirect(['view', 'id' => $model->id, "tournament_id" => $model->tournament_id]);
    }

    public function actionPrintballots($id, $debug = false)
    {
        set_time_limit(0);
        $model = Round::findOne(["id" => $id]);

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            'cssFile' => '@frontend/assets/css/ballot.css',
            'content' => $this->renderPartial("ballots", [
                "model" => $model
            ]),
            'options' => [
                'title' => 'Ballots Round #' . $model->number,
            ],
        ]);

        //renderAjax does the trick for no layout
        return $pdf->render();
    }

    /**
     * @param integer $aID UserID
     * @param integer $bID USerID
     */
    public function actionSwitchAdjudicators($id, $aID, $bID)
    {
        $a = Adjudicator::find()->tournament($this->_tournament->id)->andWhere([
            "user_id" => $aID
        ])->one();
        $b = Adjudicator::find()->tournament($this->_tournament->id)->andWhere([
            "user_id" => $bID
        ])->one();

        if ($a instanceof Adjudicator && $b instanceof Adjudicator) {
            /** @var AdjudicatorInPanel $a_in_panel */
            $a_in_panel = AdjudicatorInPanel::find()->joinWith("panel")->where([
                "adjudicator_id" => $a->id,
                "panel.used" => 1,
            ])->orderBy(["panel_id" => SORT_DESC])->one();

            $b_in_panel = AdjudicatorInPanel::find()->joinWith("panel")->where([
                "adjudicator_id" => $b->id,
                "panel.used" => 1,
            ])->orderBy(["panel_id" => SORT_DESC])->one();

            $temp = $a_in_panel->panel_id;
            $temp_pos = $a_in_panel->function;
            $a_in_panel->panel_id = $b_in_panel->panel_id;
            $a_in_panel->function = $b_in_panel->function;
            $b_in_panel->panel_id = $temp;
            $b_in_panel->function = $temp_pos;

            if ($a_in_panel->validate() && $b_in_panel->validate()) {
                if ($a_in_panel->save() && $b_in_panel->save())
                    Yii::$app->session->addFlash("success", Yii::t("app", "Adjudicator {n1} and {n2} switched", [
                        "n1" => $a->getName(),
                        "n2" => $b->getName(),
                    ]));
            } else {
                Yii::$app->session->addFlash("error", Yii::t("app", "Could not switch because: {a_panel}<br>and<br>{b_panel}", [
                    "a_panel" => ObjectError::getMsg($a_in_panel),
                    "b_panel" => ObjectError::getMsg($b_in_panel),
                ]));
                Yii::error("Adjudicator switching error: " . ObjectError::getMsg($a_in_panel) . "\n" . ObjectError::getMsg($b_in_panel), __METHOD__);
            }
        }

        $this->redirect(['view', 'id' => $id, "tournament_id" => $this->_tournament->id]);
    }

    public function actionDebatedetails()
    {
        try {
            $id = Yii::$app->request->post("expandRowKey", 0);
            $debate = Debate::findOne($id);
            if ($debate instanceof Debate)
                return $this->renderAjax("_debate_details", ["model" => $debate]);
        } catch (Exception $ex) {
            return $ex->getMessage();
        }

        return "Error";
    }

    public function actionExport($id, $type = "json")
    {
        $team_props = [
            "id", "name", "society_id", "language_status", "isSwing", "points", "speakerA_speaks", "speakerB_speaks",
        ];

        $panel_props = [
            "id", "strength", "is_preset"
        ];

        $adju_props = [
            "id", "name", "breaking", "strength", "society_id", "can_chair", "are_watched",
        ];

        $user_props = [
            "id", "name", "language_status", "gender"
        ];

        $society_props = [
            "id", "fullname", "abr"
        ];

        $country_props = [
            "id", "name", "alpha_2", "region_id"
        ];

        $round = Round::findOne(["id" => $id]);
        $societies = []; // keep a separate list of societies, so we create each once only
        if ($round instanceof Round) {

            /** @var Debate $model */
            foreach ($round->debates as $model) {

                $teams = [];
                foreach ($model->getTeams() as $pos => $team) {
                    $pos = strtoupper($pos);
                    $teams[$pos] = $team->toArray($team_props);
                    if ($team->speakerA) {
                        $teams[$pos]["speakers"]["A"] = $team->speakerA->toArray($user_props);
                        $past_society = $team->speakerA->getSocieties()->all();
                        foreach ($past_society as $s) {
                            $teams[$pos]["speakers"]["A"]["societies"][] = $s->id;
                            $societies[$s->id] = $s->toArray($society_props);
                            $societies[$s->id]["country"] = $s->country->alpha_2;
                        }
                    }
                    if ($team->speakerB) {
                        $teams[$pos]["speakers"]["B"] = $team->speakerB->toArray($user_props);
                        $past_society = $team->speakerB->getSocieties()->all();
                        foreach ($past_society as $s) {
                            $teams[$pos]["speakers"]["B"]["societies"][] = $s->id;
                            $societies[$s->id] = $s->toArray($society_props);
                            $societies[$s->id]["country"] = $s->country->alpha_2;
                        }
                    }

                    // Add society to the list of societies
                    if (!array_key_exists($team->society_id, $societies)) {
                        $societies[$team->society_id] = $team->society->toArray($society_props);
                        $societies[$team->society_id]["country"] = $team->society->country->alpha_2;
                    }
                }

                $line = [
                    "id" => $model->id,
                    "venue" => $model->venue_id,
                    "teams" => $teams,
                    "panel" => $model->panel->toArray($panel_props),
                    "messages" => $model->messages,
                    "energy" => 0,
                ];
                $line["panel"]["adjudicators"] = [];

                foreach ($model->panel->adjudicatorInPanels as $inPanel) {

                    /** @var Adjudicator $adju */
                    $adju = $inPanel->adjudicator;
                    $adju->refresh();
                    $adjudicator = $adju->toArray($adju_props);
                    $adjudicator["gender"] = $adju->user->gender;
                    $adjudiator["language"] = $adju->user->language;
                    $adjudicator["country"] = isset($adju->user->societies[0]) ?
                        $adju->user->societies[0]->country->toArray($country_props) :
                        "";
                    $adjudicator["country"]["region_name"] = isset($adju->user->societies[0]) ?
                        $adju->user->societies[0]->country->getRegionName() :
                        "";
                    $adjudicator["societies"] = ArrayHelper::getColumn($adju->getSocieties(true)->asArray()->all(), "id");
                    $adjudicator["language_status"] = $adju->user->language_status;

                    $strikedAdju = $adju->getStrikedAdjudicators()->asArray()->all();
                    $adjudicator["strikedAdjudicators"] = ArrayHelper::getColumn($strikedAdju, "id");

                    $strikedTeam = $adju->getStrikedTeams()->asArray()->all();
                    $adjudicator["strikedTeams"] = ArrayHelper::getColumn($strikedTeam, "id");

                    $adjudicator["pastAdjudicatorIDs"] = $adju->getPastAdjudicatorIDsWithRoundNumbers($round->number);
                    $adjudicator["pastTeamIDs"] = $adju->getPastTeamIDsWithRoundNumbers($round->number);

                    if ($inPanel->function == Panel::FUNCTION_CHAIR) {
                        array_unshift($line["panel"]["adjudicators"], $adjudicator);
                    } else {
                        $line["panel"]["adjudicators"][] = $adjudicator;
                    }

                    // Add societies to the list of societies
                    // The first if statement might be redundant, given the foreach loop after it
                    if (!array_key_exists($adju->society_id, $societies)) {
                        $societies[$adju->society_id] = $adju->society->toArray($society_props);
                        $societies[$adju->society_id]["country"] = $adju->society->country->alpha_2;
                    }
                    foreach ($adju->getSocieties()->all() as $society) {
                        if (!array_key_exists($society->id, $societies)) {
                            $societies[$society->id] = $society->toArray($society_props);
                            $societies[$society->id]["country"] = $society->country->alpha_2;
                        }
                    }
                }

                $DRAW[] = $line;
            }
        }

        $output = "";
        $name = str_replace(" ", "-", ($round->tournament->name . " " . $round->getName()));

        switch (strtolower($type)) {
            case "json":
                header("Content-Type: application/json");
                header("Content-Disposition: attachment; filename=" . $name . ".json");
                header('Pragma: no-cache');
                $output = json_encode(["draw" => $DRAW, "societies" => $societies]);
                break;
            case "raw":
                $apptype = "application/text";
                $filetype = "txt";
                $output = print_r(["draw" => $DRAW, "societies" => $societies], true);
                break;
        }

        return $output;
    }

    public function actionImport($id, $type = "json")
    {
        /** @var Round $model */
        $model = Round::findOne(["id" => $id]);

        if (Yii::$app->request->isPost) {

            $file = \yii\web\UploadedFile::getInstanceByName("jsonFile");

            /* Check not published and no results exist */
            $clean = true;
            $amount_results = 0;
            foreach ($model->debates as $debate) {
                /** @var Debate $debate */
                $amount_results += ($debate->result instanceof Result) ? 1 : 0;
            }

            if ($model->published || $amount_results > 0) {
                Yii::$app->session->addFlash("warning", Yii::t("app", "Round is already active! Can't override with input."));

                return $this->redirect(["round/view", "id" => $model->id, "tournament_id" => $model->tournament_id]);
            }

            if ($file instanceof UploadedFile) {
                $filecontent = file_get_contents($file->tempName);
                $json = json_decode($filecontent, true);

                /* CLEAN old data */
                $debates = Debate::findAll(["round_id" => $model->id]);
                foreach ($debates as $debate) {
                    AdjudicatorInPanel::deleteAll([
                        "panel_id" => $debate->panel_id,
                    ]);
                }

                $test_variable = count($json['draw']);

                for ($row = 0; $row < count($json['draw']); $row++) {

                    $debate = $json['draw'][$row];
                    $debate_id = $debate["id"];

                    $db_debate = Debate::findOne($debate_id);

                    if ($db_debate instanceof Debate) {

                        $strength = $debate['panel']["strength"];
                        $messages = $debate["messages"];

                        $panel = $debate["panel"];

                        $db_panel = $db_debate->panel;
                        $db_panel->strength = $strength;
                        $db_panel->save();

                        $chair_id = $panel['adjudicators'][0];
                        $db_chair = new AdjudicatorInPanel([
                            "adjudicator_id" => $chair_id['id'],
                            "panel_id" => $db_panel->id,
                            "function" => Panel::FUNCTION_CHAIR,
                        ]);
                        $db_chair->save();

                        for($countPanellists = 1; $countPanellists < count($panel['adjudicators']); $countPanellists++){
                            $adjuID = $panel['adjudicators'][$countPanellists];
                            $db_wing = new AdjudicatorInPanel([
                                "adjudicator_id" => $adjuID['id'],
                                "panel_id" => $db_panel->id,
                                "function" => Panel::FUNCTION_WING,
                            ]);
                            $db_wing->save();
                        }

                        if (isset($panel["trainees"])) {
                            foreach ($panel["trainees"] as $traineeID) {
                                $db_trainee = new AdjudicatorInPanel([
                                    "adjudicator_id" => $traineeID,
                                    "panel_id" => $db_panel->id,
                                    "function" => Panel::FUNCTION_TRAINEE,
                                ]);
                                $db_trainee->save();
                            }
                        }

                        $round = $db_panel->debate->round;
                        $newLines = $round->updateEnergy(["newPanel" => $db_panel->debate->id, "oldPanel" => $db_panel->debate->id]);
                        $round->refresh();

                        //$db_debate->messages = $messages;
                        //if (!$db_debate->save())
                        //    throw new Exception(ObjectError::getMsg($db_debate));
                    }
                }
            } else {
                Yii::$app->session->addFlash("notice", Yii::t("app", "Uploaded file was empty. Please select a file."));
            }

            return $this->redirect(["round/view", "id" => $model->id, "tournament_id" => $model->tournament_id]);
        }

        return $this->render("import", ["model" => $model]);
    }

    public function actionAdjuRemove($id, $adju_id, $debate_id)
    {
        $debate = Debate::findOne($debate_id);

        if ($debate instanceof Debate) {
            $aip = AdjudicatorInPanel::findOne([
                "panel_id" => $debate->panel_id,
                "adjudicator_id" => intval($adju_id),
            ]);
        }

        return $this->redirect(["round/view", "id" => $id, "tournament_id" => $debate->tournament_id]);
    }
}
