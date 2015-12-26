<?php

namespace frontend\controllers;

use common\components\filter\TournamentContextFilter;
use common\components\ObjectError;
use common\models\Adjudicator;
use common\models\AdjudicatorInPanel;
use common\models\Answer;
use common\models\Debate;
use common\models\Feedback;
use common\models\Question;
use common\models\search\AnswerSearch;
use common\models\search\FeedbackSearch;
use common\models\Team;
use Yii;
use yii\base\Exception;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * FeedbackController implements the CRUD actions for feedback model.
 */
class FeedbackController extends BasetournamentController
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
                        'actions' => ['create'],
                        'matchCallback' => function ($rule, $action) {
                            $openFeedback = $this->_tournament->hasOpenFeedback(Yii::$app->user->id);
                            if (is_array($openFeedback)) {
                                $debates = ArrayHelper::getColumn($openFeedback, "debate");
                                /** @var Debate $debate */
                                $debate = Debate::findOne(Yii::$app->request->get("id", 0));
                                if (is_array($debates) && in_array($debate, $debates)) {
                                    return true;
                                }
                            }

                            return false;
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'create', 'adjudicator', 'tournament', 'tabbie', 'matrix', 'export'],
                        'matchCallback' => function ($rule, $action) {
                            return ($this->_tournament->isTabMaster(Yii::$app->user->id) ||
                                $this->_tournament->isConvenor(Yii::$app->user->id) ||
                                $this->_tournament->isCA(Yii::$app->user->id));
                        }
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all feedback models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FeedbackSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->_tournament->id);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single feedback model.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the feedback model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return feedback the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected
    function findModel($id)
    {
        if (($model = feedback::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Creates a new feedback model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @param $id Feedback ID
     * @param $type Feedback Type either Feedback::FROM_CHAIR|Feedback::FROM_WING|Feedback::FROM_TEAM
     * @param $ref Reference to the Team.ID or Adjudicator.ID that made the Feedback
     * @return mixed
     * @throws Exception
     */
    public function actionCreate($id, $type, $ref)
    {

        //Yii::trace("New Feedback: id=" . $id . " type=" . $type . " ref=" . $ref, __METHOD__);
        $already_entered = false;

        $feedback = new Feedback();
        $feedback->debate_id = $id;
        $feedback->time = date("Y-m-d H:i:s");

        switch ($type) {
            case Feedback::FROM_CHAIR:
            case Feedback::FROM_WING:
                $object = AdjudicatorInPanel::findOne(["adjudicator_id" => $ref, "panel_id" => $feedback->debate->panel_id]);
                if (!($object instanceof AdjudicatorInPanel)) {
                    Yii::error("Feedback: Chair in Panel " . $feedback->debate->panel_id . " not found", __METHOD__);
                    throw new Exception(Yii::t("app", "Chair in Panel not found - type wrong?"));
                }
                $already_entered = $object->got_feedback;
                break;
            case Feedback::FROM_TEAM:
                $object = Debate::find()->tournament($this->_tournament->id)->andWhere(
                    "og_team_id = :og OR oo_team_id = :oo OR cg_team_id = :cg OR co_team_id = :co",
                    [
                        "og" => $ref,
                        "oo" => $ref,
                        "cg" => $ref,
                        "co" => $ref,
                    ]
                )->orderBy(["id" => SORT_DESC])->one();

                if (!($object instanceof Debate)) {
                    Yii::error("Feedback: Team not found!", __METHOD__);
                    throw new Exception(Yii::t("app", "Team not found - type wrong?"));
                }

                foreach ($object->getTeams(true) as $pos => $team_id) {
                    if ($team_id == $ref) {
                        $already_entered = $object->{$pos . "_feedback"};
                        $team_pos = $pos;
                    }
                }
                break;
            default:
                Yii::error("Feedback: Type goes default. Type: " . $type, __METHOD__);
                throw new Exception(Yii::t("app", "No valid type"));
        }

        $model_group = [];
        if ($type == Feedback::FROM_CHAIR) {
            foreach ($object->panel->getAdjudicators()->all() as $a) {
                if ($a->id != $ref) {
                    $model_group[] = [
                        "title" => $a->name,
                        "item" => $this->addQuestions($type),
                        "to" => $a->id,
                        "from" => $ref
                    ];
                }
            }
        } else {
            $model_group[] = [
                "title" => $object->panel->getChairInPanel()->adjudicator->name,
                "item" => $this->addQuestions($type),
                "to" => $object->panel->getChairInPanel()->adjudicator->id,
                "from" => $ref
            ];
        }

        if (Yii::$app->request->isPost && !$already_entered) {
            //Yii::trace("Was Post and not entered", __METHOD__);
            $allGood = true;
            $answers_group = Yii::$app->request->post("Answer");

            for ($group = 0; $group < count($answers_group); $group++) {

                //Yii::trace("Do Group #" . $group . " with type=" . $type, __METHOD__);
                $toOption = null;
                switch ($type) {
                    case Feedback::FROM_WING:
                        $toOption = Feedback::TO_CHAIR;
                        break;
                    case Feedback::FROM_CHAIR:
                        $toOption = Feedback::TO_WING;
                        break;
                    case Feedback::FROM_TEAM:
                        $toOption = Feedback::TO_CHAIR_FROM_TEAM;
                        break;
                }

                $feedbackIterate = clone $feedback;
                $feedbackIterate->to_id = $model_group[$group]["to"];
                $feedbackIterate->to_type = $toOption;
                $feedbackIterate->from_id = $model_group[$group]["from"];

                if (!$feedbackIterate->save()) {
                    Yii::error("Saving Feedback failed: " . ObjectError::getMsg($feedbackIterate), __METHOD__);
                }

                $answers = $answers_group[$group];

                foreach ($this->_tournament->getQuestions($type)->all() as $question) {
                    if (isset($answers[$question->id])) {
                        //Yii::trace("Add Question #" . $question->id, __METHOD__);
                        if (is_array($answers[$question->id])) {
                            $answer = json_encode($answers[$question->id]);
                        } else {
                            $answer = $answers[$question->id];
                        }

                        $model_group[$group]["item"][$question->id]->value = $answer;
                        $model_group[$group]["item"][$question->id]->feedback_id = $feedbackIterate->id;
                        $model_group[$group]["item"][$question->id]->question_id = $question->id;

                        if ($model_group[$group]["item"][$question->id]->save()) {
                            //Yii::trace("Saved Question!", __METHOD__);
                            $allGood = true;
                        } else {
                            Yii::error("Can't save Question: " . ObjectError::getMsg($model_group[$group]["item"][$question->id]), __METHOD__);
                            $allGood = false;
                        }
                    }
                }

                if ($allGood) {
                    //Yii::trace("All are good", __METHOD__);
                    switch ($type) {
                        case Feedback::FROM_CHAIR:
                        case Feedback::FROM_WING:
                            $object->got_feedback = 1;
                            break;
                        case Feedback::FROM_TEAM:
                            $object->{$team_pos . "_feedback"} = 1;
                            break;
                    }

                    if (!$object->save()) {
                        Yii::error("Save error " . ObjectError::getMsg($object), __METHOD__);
                        throw new Exception("Save error " . ObjectError::getMsg($object));
                    }
                }
                $already_entered = true;
            }
        }

        if ($already_entered) {
            //Yii::trace("SUCCESS!", __METHOD__);
            Yii::$app->session->addFlash("success", Yii::t("app", "{object} successfully submitted", ['object' => Yii::t("app", 'Feedback')]));

            return $this->redirect(['tournament/view', "id" => $this->_tournament->id]);
        } else {
            //Yii::trace("show create form", __METHOD__);
            return $this->render('create', ['model_group' => $model_group]);
        }

    }

    public function addQuestions($type)
    {
        $models = [];
        foreach ($this->_tournament->getQuestions($type)->all() as $question) {
            $models[$question->id] = new Answer([
                "question_id" => $question->id,
            ]);
        }

        return $models;
    }

    public function actionAdjudicator()
    {
        $searchModel = new AnswerSearch();
        $dataProvider = $searchModel->searchByAdjudicator(Yii::$app->request->queryParams, $this->_tournament->id);

        return $this->render('by_adjudicator', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

    }

    /**
     * Deletes an existing feedback model.
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

    public function actionTournament()
    {
        return "Soon";
    }

    public function actionTabbie()
    {
        return "Soon";
    }

    public function actionExport()
    {
        $delimiter = ",";
        $enclosure = "\"";

        $filename = $this->_tournament->name .
            "-after-" .
            str_replace(" ", "-", ($this->_tournament->getLastRound()) ? $this->_tournament->getLastRound()->getName() : "Start") .
            "-at-" .
            date("Y-m-d-H-i-s") .
            ".csv";

        $array = [
            ["RoundID", "Type",
                "From.ID", "From.Name", "From.Region", "From.OverallPoints", "From.LastRoundPoints", "From.Strength",
                "On.ID", "On.Name", "On.Region", "On.Strength"
            ],
        ];

        $questions = Question::find()
            ->joinWith("tournamentHasQuestion")
            ->where(["tournament_id" => $this->_tournament->id])
            ->all();

        foreach ($questions as $q) {
            $array[0][] = $q->text;
        }

        $feedbacks = Feedback::find()->joinWith("debate")->where([
            "tournament_id" => $this->_tournament->id,
        ])->all();

        $amountFeedbacks = count($feedbacks);
        for ($i = 0; $i < $amountFeedbacks; $i++) {
            /** @var Feedback $f */
            $f = $feedbacks[$i];
            $a = [];
            $fromObject = $f->from;
            $a[] = $f->debate->round->label;
            $a[] = $f->to_type;
            $a[] = $fromObject->id;
            $a[] = $fromObject->name;
            $a[] = $fromObject->society->country->region_id;
            if ($fromObject instanceof Adjudicator) {
                $a[] = null;
                $a[] = null;
                $a[] = $fromObject->strength;
            } else {
                $a[] = $fromObject->points;
                if ($f->debate->result) {
                    $debate = $f->debate;
                    foreach (Team::getPos() as $pos) {
                        if ($debate->{$pos . "_team_id"} == $fromObject->id) {
                            $a[] = $debate->result->getPoints($pos);
                        }
                    }
                } else {
                    $a[] = null;
                }
                $a[] = null;
            }

            $toObject = $f->to;
            $a[] = $toObject->id;
            $a[] = $toObject->name;
            $a[] = $toObject->society->country->region_id;
            $a[] = $toObject->strength;

            foreach ($questions as $q) {

                /** @var Answer $answer */
                $answer = Answer::findOne([
                    "feedback_id" => $f->id,
                    "question_id" => $q->id,
                ]);
                $a[] = $answer->value;
            }

            $array[$i + 1] = $a;
        }

        $now = gmdate("D, d M Y H:i:s");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Expires: {$now} GMT");
        header("Last-Modified: {$now} GMT");

        // force download
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");

        if (count($array) == 0) {
            return null;
        }

        ob_start();
        $df = fopen("php://output", 'w');
        foreach ($array as $row) {
            fputcsv($df, $row, $delimiter, $enclosure);
        }
        fclose($df);
        return ob_get_clean();
    }
}
