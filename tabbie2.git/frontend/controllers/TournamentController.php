<?php

namespace frontend\controllers;

use common\components\filter\TournamentContextFilter;
use common\components\ObjectError;
use common\models;
use common\models\search\TournamentSearch;
use common\models\Tournament;
use frontend\models\DebregsyncForm;
use kartik\helpers\Html;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * TournamentController implements the CRUD actions for Tournament model.
 */
class TournamentController extends BaseTournamentController
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
                        'actions' => ['index', 'archive', 'view', 'testimport'],
                        'roles' => [],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'debreg-sync'],
                        'matchCallback' => function ($rule, $action) {
                            return ($this->_tournament->isTabMaster(Yii::$app->user->id) || $this->_tournament->isConvenor(Yii::$app->user->id));
                        }
                    ],
                    [
                        'allow' => true,
                        'actions' => ['migrate-tabbie'],
                        'matchCallback' => function ($rule, $action) {
                            return ($this->_tournament->isTabMaster(Yii::$app->user->id));
                        }
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
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TournamentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Tournament models.
     *
     * @return mixed
     */
    public function actionArchive()
    {
        $searchModel = new TournamentSearch();
        $dataProvider = $searchModel->searchArchive(Yii::$app->request->queryParams);

        return $this->render('archive', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a current Tournament model.
     *
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', ['model' => $this->findModel($id),]);
    }

    /**
     * Creates a new Tournament model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Tournament();
        $model->status = Tournament::STATUS_RUNNING;

        if (Yii::$app->request->isPost) {
            $file = UploadedFile::getInstance($model, 'logo');
            $model->load(Yii::$app->request->post());
            $model->generateUrlSlug();
            if ($file instanceof UploadedFile) {
                $model->saveLogo($file);
            } else
                $model->logo = null;

            if ($model->save()) {
                $energyConf = new models\EnergyConfig();
                if ($energyConf->setup($model))
                    Yii::$app->session->addFlash("success", Yii::t("app", "Tournament successfully created"));
                else
                    Yii::$app->session->addFlash("warning", Yii::t("app", "Tournament created but Energy config failed!") . ObjectError::getMsg($energyConf));

                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash("error", Yii::t("app", "Can't save Tournament!") . ObjectError::getMsg($model));
            }
        }
        //Preset variables
        $model->tabmaster_user_id = Yii::$app->user->id;
        $model->tabAlgorithmClass = Yii::$app->params["stdTabAlgorithm"];
        return $this->render('create', ['model' => $model,]);
    }

    /**
     * Updates an existing Tournament model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost) {

            //Upload File
            $file = \yii\web\UploadedFile::getInstance($model, 'logo');

            //Save Old File Path
            $oldFile = $model->logo;
            //Load new values
            $model->load(Yii::$app->request->post());

            if ($file instanceof UploadedFile) {
                //Save new File
                $model->saveLogo($file);
            } else
                $model->logo = $oldFile;

            if ($model->save()) {
                Yii::$app->cache->set("tournament" . $model->id, $model, 120);
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', ['model' => $model,]);
    }

    /**
     * Deletes an existing Tournament model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Tournament model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Tournament the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Tournament::findByPk($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @param Tournament $tournament
     *
     * @return int|false
     */
    public function activeInputAvailable($tournament)
    {
        $user_id = Yii::$app->user->id;

        $activeRound = models\Round::findOne(["tournament_id" => $tournament->id, "displayed" => 1, "published" => 1, "closed" => 0,]);

        if ($activeRound) {
            $debate = models\Debate::findOneByChair($user_id, $tournament->id, $activeRound->id);
            if ($debate instanceof models\Debate) return $debate->id;
        }

        return false;
    }


    /**
     * Sync with DebReg System
     *
     * @param integer $id
     *
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionDebregSync($id)
    {

        $tournament = $this->findModel($id);
        $model = new DebregsyncForm();

        if (Yii::$app->request->isPost) {

            $a_fix = $t_fix = $s_fix = [];

            if (Yii::$app->request->post("mode") == "refactor") {
                $a_fix = Yii::$app->request->post("Adju", []);
                $t_fix = Yii::$app->request->post("Team", []);
                $s_fix = Yii::$app->request->post("Soc", []);
            }

            $model->load(Yii::$app->request->post());
            $model->tournament = $this->_tournament;

            $error = $model->getAccessKey();

            if ($error === true) {
                $unresolved = $model->doSync($a_fix, $t_fix, $s_fix);

                if (count($unresolved) == 0) {
                    Yii::$app->session->addFlash("success", Yii::t("app", "DebReg Syncing successful"));
                    return $this->redirect(['view', 'id' => $tournament->id]);
                } else
                    return $this->render('sync_resolve', [
                        'unresolved' => $unresolved,
                        'tournament' => $tournament,
                        'model' => $model
                    ]);
            } else {
                $model->addError("password", $error);
            }

        }

        return $this->render('sync_login', [
            'model' => $model,
            'tournament' => $tournament]);
    }

    /**
     * Migrate back to Tabbie v1
     *
     * @param    integer $id
     */
    public function actionMigrateTabbie($id)
    {
        /** Make output UTF-8 */
        mb_internal_encoding('UTF-8');
        mb_http_output('UTF-8');
        mb_http_input('UTF-8');
        mb_language('uni');
        mb_regex_encoding('UTF-8');
        ob_start('mb_output_handler');

        $tournament = $this->_tournament;

        $sqlFile = [];
        $sqlFile[] = "USE database tabbie;";
        $sqlFile[] = "";

        /** ADJUDICATORS */
        $sqlFile[] = "CREATE TABLE `adjudicator` (
  `adjud_id` MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
  `univ_id` MEDIUMINT(9) NOT NULL DEFAULT '0',
  `adjud_name` VARCHAR(100) NOT NULL DEFAULT '',
  `ranking` MEDIUMINT(9) NOT NULL DEFAULT '0',
  `active` ENUM('Y','N') NOT NULL DEFAULT 'Y',
  `status` ENUM( 'normal', 'trainee', 'watcher', 'watched' ) NOT NULL DEFAULT 'normal',
  PRIMARY KEY  (`adjud_id`),
  UNIQUE KEY `adjud_name` (`adjud_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Adjudicator Table';";

        $adju = models\Adjudicator::find()->tournament($tournament->id)->all();
        foreach ($adju as $a) {
            $society[$a->society->abr] = $a->society;
            $sqlFile[] = "INSERT INTO adjudicator VALUES(" . implode(",", [
                    $a->id,
                    $this->strquote($a->user->name)
                ]) . ");";
        }

        /** TEAMS */

        $sqlFile[] = "";
        $sqlFile[] = "CREATE TABLE `speaker` (
  `speaker_id` MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
  `team_id` MEDIUMINT(9) NOT NULL DEFAULT '0',
  `speaker_name` VARCHAR(100) NOT NULL DEFAULT '',
  `speaker_esl` CHAR(3) NOT NULL DEFAULT 'N',
  `speaker_novice` CHAR(1) NOT NULL DEFAULT 'N',
  PRIMARY KEY  (`speaker_id`),
  UNIQUE KEY `team_id` (`team_id`,`speaker_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Speaker Table';";

        $sqlFile[] = "CREATE TABLE `team` (
  `team_id` MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
  `univ_id` MEDIUMINT(9) NOT NULL DEFAULT '0',
  `team_code` VARCHAR(50) NOT NULL DEFAULT '',
  `esl` VARCHAR(3) DEFAULT NULL,
  `novice` VARCHAR(3) DEFAULT NULL,
  `active` ENUM('N','Y') NOT NULL DEFAULT 'N',
  `composite` ENUM('N','Y') NOT NULL DEFAULT 'Y',
  PRIMARY KEY  (`team_id`),
  UNIQUE KEY `univ_id` (`univ_id`,`team_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Team Table';";

        $teams = models\Team::find()->tournament($tournament->id)->all();
        foreach ($teams as $t) {
            $society[$t->society->abr] = $t->society;

            $speaker[] = $t->speakerA;
            $speaker[] = $t->speakerB;

            $sqlFile[] = "INSERT INTO team VALUES(".implode(",", [
                    $t->id,
                    $t->society->id,
                    $this->strquote($t->name),
                    ($t->language_status == models\User::LANGUAGE_ESL) ? "'esl'" : "'non'",
                    (isset($t->novice) && $t->novice) ? "'Y'" : "'non'", //Not yet implemented
                    ($t->active) ? "'Y'" : "'N'",
                    "'N'"
                ]).");";

            foreach(models\Team::getSpeaker() as $p)
            {
                $sp = $t->{"speaker".$p};
                if($sp instanceof models\User) { //Could be iron man
                    $sqlFile[] = "INSERT INTO speaker VALUES(" . implode(",", [
                            $sp->id,
                            $t->id,
                            $this->strquote($sp->name),
                            ($sp->language_status == models\User::LANGUAGE_ESL) ? "'Y'" : "'N'",
                            (isset($sp->novice) && $sp->novice) ? "'Y'" : "'non'", //Not yet implemented
                        ]) . ");";
                }
            }
        }

        $sqlFile[] = "";
        $sqlFile[] = "CREATE TABLE `university` (
  `univ_id` MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
  `univ_name` VARCHAR(100) NOT NULL DEFAULT '',
  `univ_code` VARCHAR(20) NOT NULL DEFAULT '',
  PRIMARY KEY  (`univ_id`),
  UNIQUE KEY `univ_code` (`univ_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='University Table';";

        foreach($society as $s)
        {
            $sqlFile[] = "INSERT INTO university VALUES(".implode(",", [
                    $s->id,
                    $this->strquote($s->fullname),
                    $this->strquote($s->abr)
                ]).");";
        }

        /** Venues */
        $sqlFile[] = "";
        $sqlFile[] = "CREATE TABLE `venue` (
  `venue_id` MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
  `venue_name` VARCHAR(50) NOT NULL DEFAULT '',
  `venue_location` VARCHAR(50) NOT NULL DEFAULT '',
  `active` ENUM('Y','N') NOT NULL DEFAULT 'Y',
  PRIMARY KEY  (`venue_id`),
  UNIQUE KEY `venue_name` (`venue_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Venue Table';";

        $venues = models\Venue::find()->tournament($tournament->id)->all();
        foreach ($venues as $v) {
            $sqlFile[] = "INSERT INTO venue VALUES(".implode(",", [
                    $v->id,
                    $this->strquote($v->name),
                    $this->strquote($v->group),
                    ($v->active) ? "'Y'" : "'N'",
                ]).");";
        }

        $sqlFile[] = "";
        $sqlFile[] = "CREATE TABLE `motions` (
  `round_no` SMALLINT(6) NOT NULL DEFAULT '0',
  `motion` TEXT NOT NULL,
  `info_slide` ENUM('Y','N') NOT NULL DEFAULT 'N',
  `info` TEXT NOT NULL,
  PRIMARY KEY  (`round_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

        $sqlFile[] = "";
        $sqlFile[] = "CREATE TABLE draws (
	`round_no` MEDIUMINT(9) NOT NULL,
	`debate_id` MEDIUMINT(9) NOT NULL ,
	`og` MEDIUMINT(9) NOT NULL ,
	`oo` MEDIUMINT(9) NOT NULL ,
	`cg` MEDIUMINT(9) NOT NULL ,
	`co` MEDIUMINT(9) NOT NULL ,
	`venue_id` MEDIUMINT(9) NOT NULL ,
	PRIMARY KEY (debate_id, round_no)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Draws Table';
";

        $sqlFile[] = "";
        $sqlFile[] = "CREATE TABLE draw_adjud (
	`round_no` MEDIUMINT(9) NOT NULL,
	`debate_id` MEDIUMINT(9) NOT NULL,
	`adjud_id` MEDIUMINT(9) NOT NULL,
	`status` ENUM( 'chair', 'panelist', 'trainee' ) NOT NULL ,
	PRIMARY KEY (`round_no`, `debate_id`, `adjud_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Adjudicator Allocations table';
";

        $sqlFile[] = "";
        $sqlFile[] = "CREATE TABLE `results` (
	`round_no` MEDIUMINT(9) NOT NULL,
	`debate_id` MEDIUMINT(9) NOT NULL DEFAULT '0',
	`first` MEDIUMINT(9) NOT NULL DEFAULT '0',
	`second` MEDIUMINT(9) NOT NULL DEFAULT '0',
	`third` MEDIUMINT(9) NOT NULL DEFAULT '0',
	`fourth` MEDIUMINT(9) NOT NULL DEFAULT '0',
	PRIMARY KEY  (`debate_id`, `round_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Team results';
";

        $sqlFile[] = "CREATE TABLE `speaker_results` (
	`round_no` MEDIUMINT(9) NOT NULL DEFAULT '0',
	`speaker_id` MEDIUMINT(9) NOT NULL DEFAULT '0',
	`debate_id` MEDIUMINT(9) NOT NULL DEFAULT '0',
	`points` SMALLINT(9) NOT NULL DEFAULT '0',
	PRIMARY KEY (`speaker_id`, `round_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Speaker results';
";
        foreach ($tournament->rounds as $round) {
            /** ROUND */
            /** @var models\Round $round */

            $sqlFile[] = "";
            $sqlFile[] = "CREATE TABLE `result_round_" . $round->number . "`(
	`debate_id` MEDIUMINT(9) NOT NULL DEFAULT '0',
	`first` MEDIUMINT(9) NOT NULL DEFAULT '0',
	`second` MEDIUMINT(9) NOT NULL DEFAULT '0',
	`third` MEDIUMINT(9) NOT NULL DEFAULT '0',
	`fourth` MEDIUMINT(9) NOT NULL DEFAULT '0',
	PRIMARY KEY  (`debate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Team results';";

            foreach ($round->debates as $debate) {
                /** DEBATE */
                /** @var models\Debate $debate */


                /** RESULT */
                if ($debate->result instanceof models\Result) // There might not be a result yet
                {
                    /** @var models\Result $result */
                    $result = $debate->result;

                    $values = [$debate->id];
                    $values[$result->og_place] = $debate->og_team_id;
                    $values[$result->oo_place] = $debate->oo_team_id;
                    $values[$result->cg_place] = $debate->cg_team_id;
                    $values[$result->co_place] = $debate->co_team_id;

                    $sqlFile[] = "INSERT INTO result_round_" . $round->number . " VALUES(" . implode(",", $values) . ")";
                }
            }
        }

        $sqlFile[] = "CREATE TABLE `strikes` (
  `adjud_id` INT(11) NOT NULL,
  `team_id` INT(11) DEFAULT NULL,
  `univ_id` INT(11) DEFAULT NULL,
  `strike_id` INT(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY  (`strike_id`),
  KEY `univ_id` (`univ_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Conflict Table';";

        echo implode("<br>\n", $sqlFile);
        exit();
    }

    private function strquote($str)
    {
        return "'" . $str . "'";
    }
}
