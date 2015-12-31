<?php

namespace common\models;

use algorithms\algorithms\StrictWUDCRules;
use common\components\ObjectError;
use kartik\helpers\Html;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * This is the model cla ss for table "round".
 *
 * @property integer $id
 * @property integer $number
 * @property string $label
 * @property string $level
 * @property integer $tournament_id
 * @property integer $type
 * @property integer $energy
 * @property string $motion
 * @property string $infoslide
 * @property string $time
 * @property bool $published
 * @property bool $displayed
 * @property bool $closed
 * @property float $lastrun_temp
 * @property integer $lastrun_time
 * @property datetime $prep_started
 * @property datetime $finished_time
 * @property TabAfterRound[] $tabAfterRounds
 * @property Tournament $tournament
 * @property Tag[] $tags
 * @property MotionTag[] $motionTags
 */
class Round extends \yii\db\ActiveRecord
{

    const STATUS_CREATED = 0;
    const STATUS_PUBLISHED = 1;
    const STATUS_DISPLAYED = 2;
    const STATUS_STARTED = 3;
    const STATUS_JUDGING = 4;
    const STATUS_CLOSED = 5;

    const TYP_IN = 0;
    const TYP_OUT = 1;
    const TYP_ESL = 2;
    const TYP_EFL = 3;
    const TYP_NOVICE = 4;

    public $round_tags = [];

    static function statusLabel($code = null)
    {

        $labels = [
            0 => Yii::t("app", "Created"),
            1 => Yii::t("app", "Published"),
            2 => Yii::t("app", "Displayed"),
            3 => Yii::t("app", "Started"),
            4 => Yii::t("app", "Judging"),
            5 => Yii::t("app", "Finished"),
        ];

        return (is_numeric($code)) ? $labels[$code] : $labels;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'round';
    }

    public function getTypeOptions()
    {
        $t = $this->tournament;

        $options[self::TYP_OUT] = Yii::t("app", "Main");
        if ($t->has_esl) {
            $options[self::TYP_ESL] = Yii::t("app", "ESL");
        }
        //if($t->has_efl)
        //$options[self::TYP_EFL] = Yii::t("app", "EFL");
        $options[self::TYP_NOVICE] = Yii::t("app", "Novice");

        return $options;
    }

    public function getLevelOptions()
    {
        $options = [];
        $t = $this->tournament;
        if ($t->has_final) {
            $options[1] = Yii::t("app", "Final");
        }
        if ($t->has_semifinal) {
            $options[2] = Yii::t("app", "Semifinal");
        }
        if ($t->has_quarterfinal) {
            $options[4] = Yii::t("app", "Quarterfinal");
        }
        if ($t->has_octofinal) {
            $options[8] = Yii::t("app", "Octofinal");
        }

        return $options;
    }

    public function getTags()
    {
        return $this->hasMany(Tag::className(), ['round_id' => 'id']);
    }

    public function setTags($value)
    {
        Tag::deleteAll(["round_id" => $this->id]);
        $tags = [];
        if (is_array($value)) {
            foreach ($value as $t) {
                if (!is_numeric($t)) {
                    $new_Tag = new MotionTag([
                        "name" => ucwords(htmlentities(trim($t))),
                    ]);
                    $new_Tag->save();
                    $t = $new_Tag->id;
                }

                $tag = new Tag([
                    "motion_tag_id" => $t,
                    "round_id" => $this->id,
                ]);
                if ($tag->save())
                    $tags[] = $tag;
            }
        }

        return $tags;
    }

    /**
     * After Save
     * Save Tags not that round is available
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        $this->setTags($this->round_tags);
        parent::afterSave($insert, $changedAttributes);
    }

    public function afterFind()
    {
        $this->round_tags = ArrayHelper::getColumn($this->tags, "motion_tag_id");
        parent::afterFind();
    }

    public function getName()
    {
        if ($this->type == self::TYP_IN && $this->label <= $this->tournament->expected_rounds) {
            return Yii::t("app", "Round #{num}", ["num" => $this->getNumber()]);
        } else {
            return $this->getOutRoundName();
        }
    }

    public function getNumber()
    {
        return intval($this->label);
    }

    public function getOutRoundName()
    {
        return (($this->type != self::TYP_OUT) ? $this->getTypeLabel() . " " : "") . $this->getLevelLabel();
    }

    public function getTypeLabel()
    {
        $options = [
            self::TYP_IN => Yii::t("app", "Inround"),
            self::TYP_OUT => Yii::t("app", "Outround"),
            self::TYP_NOVICE => Yii::t("app", "Novice"),
            self::TYP_ESL => Yii::t("app", "ESL"),
            self::TYP_EFL => Yii::t("app", "EFL"),
        ];

        return $options[$this->type];
    }

    public function getLevelLabel()
    {
        switch ($this->level) {
            case 1:
                return Yii::t("app", "Final");
            case 2:
                return Yii::t("app", "Semifinal");
            case 4:
                return Yii::t("app", "Quarterfinal");
            case 8:
                return Yii::t("app", "Octofinal");
        }
    }

    public function setNumber($value)
    {
        $this->label = $value;
    }

    public function setNextRound()
    {
        $t = $this->tournament;
        $rounds = Round::find()
            ->where(["tournament_id" => $t->id])
            ->orderBy(["label" => SORT_ASC])
            ->asArray()
            ->all();
        if (!$rounds) {
            $this->label = 1;
            $this->type = self::TYP_IN;
            $this->level = 0;
        } else {
            if (count($rounds) >= $t->expected_rounds) {
                $this->label = "X";

                if ($t->has_final && in_array(2, ArrayHelper::getColumn($rounds, "level")) || !$t->has_semifinal) {
                    $this->label = "final";
                    $this->level = 1;
                } else {
                    if ($t->has_semifinal && in_array(4, ArrayHelper::getColumn($rounds, "level")) || !$t->has_quarterfinal) {
                        $this->label = "semi";
                        $this->level = 2;
                    } else {
                        if (
                            $t->has_quarterfinal && in_array(8, ArrayHelper::getColumn($rounds, "level")) || !$t->has_octofinal
                        ) {
                            $this->label = "quarter";
                            $this->level = 4;
                        } else {
                            if ($t->has_octofinal) {
                                $this->label = "octo";
                                $this->level = 8;
                            }
                        }
                    }
                }
                $this->type = self::TYP_OUT;
            } else {
                $lastRound = array_pop($rounds);
                $this->label = (intval($lastRound["label"]) + 1);
                $this->type = self::TYP_IN;
                $this->level = 0;
            }
        }
    }

    /**
     * @inheritdoc
     * @return TournamentQuery
     */
    public static function find()
    {
        return new TournamentQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['number', 'tournament_id', 'motion', 'round_tags'], 'required'],
            [['id', 'number', 'tournament_id', 'published', 'type', 'level'], 'integer'],
            [['motion', 'infoslide'], 'string'],
            [['time', 'tags'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'Round') . ' ' . Yii::t('app', 'ID'),
            'label' => Yii::t('app', 'Round'),
            'tournament_id' => Yii::t('app', 'Tournament') . ' ' . Yii::t('app', 'ID'),
            'energy' => Yii::t('app', 'Energy'),
            'motion' => Yii::t('app', 'Motion'),
            'infoslide' => Yii::t('app', 'Info Slide'),
            'time' => Yii::t('app', 'Time'),
            'published' => Yii::t('app', 'Published'),
            'displayed' => Yii::t('app', 'Displayed'),
            'prep_started' => Yii::t('app', 'PrepTime started'),
            'lastrun_temp' => Yii::t('app', 'Last Temperature'),
            'lastrun_time' => Yii::t('app', 'ms to calculate'),
            'round_tags' => Yii::t("app", 'Motion Tags'),
        ];
    }

    public function getStatus()
    {

        if ($this->hasAllResultsEntered()) {
            return Round::STATUS_CLOSED;
        }
        if ($this->isJudgingTime()) {
            return Round::STATUS_JUDGING;
        }
        if ($this->isStartingTime()) {
            return Round::STATUS_STARTED;
        }
        if ($this->displayed == 1) {
            return Round::STATUS_DISPLAYED;
        }
        if ($this->published == 1) {
            return Round::STATUS_PUBLISHED;
        }
        if ($this->time) {
            return Round::STATUS_CREATED;
        }

        throw new Exception("Unknow Round status for Round" . $this->number . " No create time");
    }

    public function hasAllResultsEntered()
    {
        $remainingResults = Debate::find()
            ->tournament($this->tournament_id)
            ->andWhere("NOT EXISTS (SELECT 1 FROM result WHERE debate.id = result.debate_id)")
            ->andWhere(["round_id" => $this->id])->count();

        if ($remainingResults == 0) {
            return true;
        }
        return false;
    }

    public function isJudgingTime()
    {
        $debatetime = (8 * 7) + 8;
        $preptime = 15;
        if ($this->prep_started) {
            $judgeTime = strtotime($this->prep_started) + $preptime + $debatetime;

            if (time() > $judgeTime) {
                return true;
            }
        }

        return false;
    }

    public function isStartingTime()
    {
        $preptime = 15;
        if ($this->prep_started) {
            $prepende = strtotime($this->prep_started) + $preptime;

            if (time() > $prepende) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDrawAfterRounds()
    {
        return $this->hasMany(TabAfterRound::className(), ['round_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTournament()
    {
        return $this->hasOne(Tournament::className(), ['id' => 'tournament_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDebates()
    {
        return $this->hasMany(Debate::className(), ['round_id' => 'id', 'tournament_id' => 'tournament_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMotionTags()
    {
        return $this->hasMany(MotionTag::className(), ['id' => 'motion_tag_id'])->viaTable('tag', ['round_id' => 'id']);
    }

    /**
     * Generate a draw for the model
     */
    public function generateWorkingDraw()
    {
        try {
            set_time_limit(0); //Prevent timeout ... this can take time

            $venues = Venue::find()->active()->tournament($this->tournament->id)->asArray()->all();
            $teams = Team::find()->active()->tournament($this->tournament->id)->asArray()->all();

            $adjudicators_Query = Adjudicator::find()->active()->tournament($this->tournament->id);

            $adjudicatorsObjects = $adjudicators_Query->all();

            $panel = [];
            $panelsObjects = Panel::find()->where([
                'is_preset' => 1,
                'used' => 0,
                'tournament_id' => $this->tournament_id])->all();

            $active_rooms = (count($teams) / 4);

            $AdjIDsalreadyinPanels = [];

            foreach ($panelsObjects as $p) {
                $panelAdju = [];
                $total = 0;

                /** @var Panel $p */
                foreach ($p->getAdjudicatorsObjects() as $adju) {
                    /** @var Adjudicator $adju */
                    $AdjIDsalreadyinPanels[] = $adju->id;

                    $adjudicator = $adju->attributes;
                    $adjudicator["name"] = $adju->name;
                    $adjudicator["societies"] = ArrayHelper::getColumn($adju->getSocieties(true)->asArray()->all(), "id");

                    $strikedAdju = $adju->getStrikedAdjudicators()->asArray()->all();
                    $adjudicator["strikedAdjudicators"] = $strikedAdju;

                    $strikedTeam = $adju->getStrikedTeams()->asArray()->all();
                    $adjudicator["strikedTeams"] = $strikedTeam;

                    $adjudicator["pastAdjudicatorIDs"] = $adju->getPastAdjudicatorIDs($this->id);
                    $adjudicator["pastTeamIDs"] = $adju->getPastTeamIDs($this->id);

                    $total += $adju->strength;

                    $panelAdju[] = $adjudicator;
                }

                $panel[] = [
                    "id" => $p->id,
                    "strength" => intval($total / count($panelAdju)),
                    "adju" => $panelAdju,
                ];
            }

            $adjudicators = [];
            for ($i = 0; $i < count($adjudicatorsObjects); $i++) {

                if (!in_array($adjudicatorsObjects[$i]->id, $AdjIDsalreadyinPanels)) {
                    //Only add if not already in Preset Panel
                    /** @var $adjudicatorsObjects [$i] Adjudicator */
                    $adjudicators[$i] = $adjudicatorsObjects[$i]->attributes;
                    $adjudicators[$i]["name"] = $adjudicatorsObjects[$i]->name;
                    $adjudicators[$i]["societies"] = ArrayHelper::getColumn($adjudicatorsObjects[$i]->getSocieties(true)->asArray()->all(), "id");

                    $strikedAdju = $adjudicatorsObjects[$i]->getStrikedAdjudicators()->asArray()->all();
                    $adjudicators[$i]["strikedAdjudicators"] = $strikedAdju;

                    $strikedTeam = $adjudicatorsObjects[$i]->getStrikedTeams()->asArray()->all();
                    $adjudicators[$i]["strikedTeams"] = $strikedTeam;

                    $adjudicators[$i]["pastAdjudicatorIDs"] = $adjudicatorsObjects[$i]->getPastAdjudicatorIDs();
                    $adjudicators[$i]["pastTeamIDs"] = $adjudicatorsObjects[$i]->getPastTeamIDs();
                }
            }

            $adjudicators_strengthArray = ArrayHelper::getColumn(
                $adjudicators_Query->select("strength")->asArray()->all(),
                "strength"
            );

            /* Check variables */
            if (count($teams) < 4) {
                throw new Exception(Yii::t("app", "Not enough Teams to fill a single room - (active: {teams_count})", ["teams_count" => count($teams)]), "500");
            }
            if (count($adjudicatorsObjects) < 2) {
                throw new Exception(Yii::t("app", "At least two Adjudicators are necessary - (active: {count_adju})", ["count_adju" => count($adjudicatorsObjects)]), "500");
            }
            if (count($teams) % 4 != 0) {
                throw new Exception(Yii::t("app", "Amount of active Teams must be divided by 4 ;) - (active: {count_teams})", ["count_teams" => count($teams)]), "500");
            }
            if ($active_rooms > count($venues)) {
                throw new Exception(Yii::t("app", "Not enough active Rooms (active: {active_rooms} required: {required})", [
                    "active_rooms" => count($venues),
                    "required" => $active_rooms,
                ]), "500");
            }
            if ($active_rooms > count($adjudicatorsObjects)) {
                throw new Exception(Yii::t("app", "Not enough adjudicators (active: {active}  min-required: {required})", [
                    "active" => count($adjudicatorsObjects),
                    "required" => $active_rooms,
                ]), "500");
            }
            if ($active_rooms > (count($adjudicators) + count($panel))) {
                throw new Exception(Yii::t("app",
                    "Not enough free adjudicators with this preset panel configuration. (fillable rooms: {active}  min-required: {required})", [
                        "active" => (count($adjudicatorsObjects) + count($panelsObjects)),
                        "required" => $active_rooms,
                    ]), "500");
            }

            /* Setup */
            /** @var StrictWUDCRules $algo */
            $algo = $this->tournament->getTabAlgorithmInstance();
            $algo->tournament_id = $this->tournament->id;
            $algo->energyConfig = EnergyConfig::loadArray($this->tournament->id);
            $algo->round_number = $this->number;

            if (count($adjudicators_strengthArray) == 0) {
                $algo->average_adjudicator_strength = 0;
                $algo->SD_of_adjudicators = 0;
            } else {
                $algo->average_adjudicator_strength = array_sum($adjudicators_strengthArray) / count($adjudicators_strengthArray);
                $algo->SD_of_adjudicators = self::stats_standard_deviation($adjudicators_strengthArray);
            }

            Yii::trace("Ready to set the draw", __METHOD__);

            $draw = $algo->makeDraw($venues, $teams, $adjudicators, $panel);

            Yii::trace("makeDraw returns " . count($draw) . "lines", __METHOD__);

            $this->saveDraw($draw);

            Yii::trace("We saved the draw.", __METHOD__);

            $this->lastrun_temp = $algo->temp;
            $this->energy = $algo->best_energy;

            return true;
        } catch (Exception $ex) {
            //throw $ex;
            $this->addError("TabAlgorithm", $ex->getMessage());
        } catch (\Exception $ex) {
            $this->addError("TabAlgorithm", $ex->getMessage());
        }

        return false;
    }

    public static function stats_standard_deviation(array $a)
    {
        $n = count($a);
        if ($n === 0) {
            trigger_error("The array has zero elements", E_USER_WARNING);

            return false;
        }
        if ($n === 1) {
            trigger_error("The array has only 1 element", E_USER_WARNING);

            return false;
        }
        $mean = array_sum($a) / $n;
        $carry = 0.0;
        foreach ($a as $val) {
            $d = ((double)$val) - $mean;
            $carry += $d * $d;
        };

        return sqrt($carry / $n);
    }

    /**
     * Saves a full draw
     *
     * @param DrawLine[] $draw
     *
     * @throws \yii\base\Exception
     */
    protected function saveDraw($draw)
    {
        Yii::trace("Save Draw with " . count($draw) . "lines", __METHOD__);
        $set_pp = 0;
        $lineAdju_total = 0;
        foreach ($draw as $line) {
            /* @var $line DrawLine */

            if (!$line->hasPresetPanel) {
                $panel = new Panel();
                $panel->tournament_id = $this->tournament_id;
                $panel->strength = $line->strength;

                //Save Panel
                if (!$panel->save()) {
                    throw new Exception(Yii::t("app", "Can't save Panel! Error: {message}", ["message" => ObjectError::getMsg($panel)]));
                }

                $line->panelID = $panel->id;

                $chairSet = false;
                foreach ($line->adjudicators as $judge) {
                    try {
                        /* @var $judge Adjudicator */
                        $alloc = new AdjudicatorInPanel();
                        $alloc->adjudicator_id = $judge["id"];
                        $alloc->panel_id = $line->panelID;
                        if (!$chairSet) {
                            $alloc->function = Panel::FUNCTION_CHAIR;
                            $chairSet = true; //only on first run
                        } else {
                            $alloc->function = Panel::FUNCTION_WING;
                        }

                        if (!$alloc->save()) {
                            throw new Exception($judge["name"] . " could not be saved: " . ObjectError::getMsg($alloc));
                        }

                    } catch (Exception $ex) {
                        Yii::error($judge["id"] . "-" . $judge["name"] . ": " . $ex->getMessage(), __METHOD__);
                        Yii::$app->session->addFlash("error", $judge["id"] . "-" . $judge["name"] . ": " . $ex->getMessage());
                    }
                }
            } else {
                //is a preset Panel
                $presetP = Panel::find()->tournament($this->tournament_id)->andWhere(["id" => $line->panelID])->one();
                $alreadyIn = ArrayHelper::getColumn($presetP->getAdjudicatorsObjects(), "id");
                $presetP->used = 1;

                if (!$presetP->save()) {
                    Yii::error("Cant save preset panel" . ObjectError::getMsg($presetP), __METHOD__);
                } else {
                    $set_pp++;
                }

                foreach ($line->adjudicators as $judge) {
                    try {
                        if (!in_array($judge["id"], $alreadyIn)) {
                            /* @var $judge Adjudicator */
                            $alloc = new AdjudicatorInPanel();
                            $alloc->adjudicator_id = $judge["id"];
                            $alloc->panel_id = $line->panelID;
                            $alloc->function = Panel::FUNCTION_WING;

                            if (!$alloc->save()) {
                                throw new Exception($judge["name"] . " could not be saved: " . ObjectError::getMsg($alloc));
                            }
                        }

                    } catch (Exception $ex) {
                        Yii::error($judge["id"] . "-" . $judge["name"] . ": " . $ex->getMessage(), __METHOD__);
                        Yii::$app->session->addFlash("error", $judge["id"] . "-" . $judge["name"] . ": " . $ex->getMessage());
                    }
                }
            }

            $debate = new Debate();
            $debate->round_id = $this->id;
            $debate->tournament_id = $this->tournament_id;
            $debate->og_team_id = $line->OG["id"];
            $debate->oo_team_id = $line->OO["id"];
            $debate->cg_team_id = $line->CG["id"];
            $debate->co_team_id = $line->CO["id"];
            $debate->venue_id = $line->venue["id"];
            $debate->panel_id = $line->panelID;
            $debate->energy = $line->energyLevel;
            $debate->setMessages($line->messages);

            if (!$debate->save()) {
                throw new Exception(Yii::t("app", "Can't save Debate! Error: {message}", ["message" => print_r($debate->getErrors(), true)]));
            } else {
                $lineAdju = $debate->panel->getAdjudicators()->count();
                $lineAdju_total += $lineAdju;
                Yii::trace("Debate #" . $debate->id . " saved with " . $lineAdju . " Adjudicators", __METHOD__);
            }
        }
        Yii::trace($set_pp . " PP saved as used", __METHOD__);
        Yii::trace($lineAdju_total . " Adjudicators saved", __METHOD__);
    }

    public function improveAdjudicator($runs)
    {
        set_time_limit(0); //Prevent timeout ... this can take time

        /** @var DrawLine[] $DRAW */
        $DRAW = [];

        if (is_int(intval($runs)) && $runs <= 100000) {
            $runs = intval($runs);
        } else {
            $runs = null;
        }

        /* Reconstruct DrawArray */
        Yii::beginProfile("Reconstruct DrawArray");
        $models = $this->debates;
        foreach ($models as $model) {

            $line = $this->reconstructDebate($model);
            $line = $this->reconstructPanel($model->panel->adjudicatorInPanels, $line);

            $DRAW[] = $line;
        }

        /** Delete Debates */
        foreach ($models as $debate) {
            /** @var Debate $debate * */
            foreach ($debate->panel->adjudicatorInPanels as $aj) {
                $aj->delete();
            }

            $panelid = $debate->panel_id;
            $debate->delete();
            Panel::deleteAll(["id" => $panelid]);
        }
        Yii::endProfile("Reconstruct DrawArray");

        /* Setup */
        $algo = $this->tournament->getTabAlgorithmInstance();
        $algo->tournament_id = $this->tournament->id;
        $algo->energyConfig = EnergyConfig::loadArray($this->tournament->id);
        $algo->round_number = $this->number;

        $adjudicators_Query = Adjudicator::find()->active()->tournament($this->tournament->id);
        $adjudicators_strengthArray = ArrayHelper::getColumn(
            $adjudicators_Query->select("strength")->asArray()->all(),
            "strength"
        );

        $algo->average_adjudicator_strength = array_sum($adjudicators_strengthArray) / count($adjudicators_strengthArray);
        $algo->SD_of_adjudicators = self::stats_standard_deviation($adjudicators_strengthArray);

        Yii::beginProfile("Improve Draw by " . $runs);

        $algo->setDraw($DRAW);
        $new_draw = $algo->optimiseAdjudicatorAllocation($runs, $this->lastrun_temp);
        $this->saveDraw($new_draw);

        $this->lastrun_temp = $algo->temp;
        $this->energy = $algo->best_energy;

        Yii::endProfile("Improve Draw by " . $runs);

        return true;
    }

    private function reconstructDebate($model)
    {
        /** @var Debate $model */
        $line = new DrawLine([
            "id" => $model->id,
            "venue" => $model->venue->attributes,
            "teamsByArray" => [
                Team::OG => $model->og_team->attributes,
                Team::OO => $model->oo_team->attributes,
                Team::CG => $model->cg_team->attributes,
                Team::CO => $model->co_team->attributes,
            ],
            "panelID" => $model->panel_id,
            "energyLevel" => $model->energy,
            "messages" => $model->getMessages(),
        ]);

        return $line;
    }

    /**
     * @param $adjudicatorInPanels
     * @param DrawLine $drawline
     * @return mixed
     */
    private function reconstructPanel($adjudicatorInPanels, $drawline)
    {
        /** @var Panel $panel */
        foreach ($adjudicatorInPanels as $inPanel) {

            $adju = $inPanel->adjudicator;
            $adjudicator = $adju->attributes;
            $adjudicator["name"] = $adju->name;
            $adjudicator["societies"] = ArrayHelper::getColumn($adju->getSocieties(true)->asArray()->all(), "id");

            $strikedAdju = $adju->getStrikedAdjudicators()->asArray()->all();
            $adjudicator["strikedAdjudicators"] = $strikedAdju;

            $strikedTeam = $adju->getStrikedTeams()->asArray()->all();
            $adjudicator["strikedTeams"] = $strikedTeam;

            $adjudicator["pastAdjudicatorIDs"] = $adju->getPastAdjudicatorIDs($this->id);
            $adjudicator["pastTeamIDs"] = $adju->getPastTeamIDs(true);

            if ($inPanel->function == Panel::FUNCTION_CHAIR) {
                $drawline->addChair($adjudicator);
            } else {
                $drawline->addAdjudicator($adjudicator);
            }
        }

        return $drawline;
    }

    /**
     * Update the Energy of certain lines and updates the database with the new energy and messages.
     *
     * @param array $updateLines
     *
     * @return array
     * @throws \yii\base\Exception
     */
    public function updateEnergy($updateLines = [])
    {
        /** @var DrawLine[] $DRAW */
        $miniDraw = [];

        /* Reconstruct DrawArray */
        foreach ($updateLines as $key => $updateline) {

            $model = Debate::findOne($updateline);
            if ($model instanceof Debate) {
                /** @var Debate $model */
                $drawline = $this->reconstructDebate($model);
                $drawline = $this->reconstructPanel($model->panel->adjudicatorInPanels, $drawline);

                $miniDraw[$key] = $drawline;
            }
        }

        /* Setup */
        /** @var StrictWUDCRules $algo */
        $algo = $this->tournament->getTabAlgorithmInstance();
        $algo->tournament_id = $this->tournament->id;
        $algo->energyConfig = EnergyConfig::loadArray($this->tournament->id);
        $algo->round_number = $this->number;

        $adjudicators_Query = Adjudicator::find()->active()->tournament($this->tournament->id);
        $adjudicators_strengthArray = ArrayHelper::getColumn(
            $adjudicators_Query->select("strength")->asArray()->all(),
            "strength"
        );

        $algo->average_adjudicator_strength = array_sum($adjudicators_strengthArray) / count($adjudicators_strengthArray);
        $algo->SD_of_adjudicators = self::stats_standard_deviation($adjudicators_strengthArray);

        $returnLine = [];
        foreach ($miniDraw as $key => $miniline) {
            $newLine = $algo->calcEnergyLevel($miniline);
            $debate = Debate::findOne($newLine->id);
            if ($debate instanceof Debate) {
                $debate->energy = $newLine->energyLevel;
                $debate->setMessages($newLine->messages);
                if (!$debate->save()) {
                    throw new Exception(
                        Yii::t("app", "Can't save debate! Errors:<br>{errors}", [
                            "errors" => ObjectError::getMsg($debate),
                        ])
                    );
                }
            } else {
                Yii::$app->session->addFlash("error", Yii::t("app", "No Debate #{num} found to update", ["num" => $newLine->id]));
            }

            $returnLine[$key] = $newLine;
        }

        return $returnLine;
    }

    public function getAmountSwingTeams()
    {
        return Team::find()->active()->tournament($this->tournament_id)->andWhere(["isSwing" => 1])->count();
    }

    public function generateBalanceSVG($size = 60)
    {
        $posMatrix = [
            "og" => 0,
            "og_x" => 0,
            "og_y" => 0,
            "oo" => 0,
            "oo_x" => 0,
            "oo_y" => 0,
            "cg" => 0,
            "cg_x" => 0,
            "cg_y" => 0,
            "co" => 0,
            "co_x" => 0,
            "co_y" => 0,
        ];
        $base = $size / 2;
        $color = "#AAF;";
        $gray = "#555";
        $factor = 3;
        $sum = 0;

        foreach ($this->getDebates()->all() as $debate) {
            $result = $debate->result;
            if ($result instanceof Result) {
                foreach (Team::getPos() as $pos) {
                    $posMatrix[$pos] += $result->{$pos . "_place"};
                    $sum += $result->{$pos . "_place"} / $factor;
                }
            }
        }

        if ($sum > 0) {
            foreach ($posMatrix as $pos => $pm) {
                $posMatrix[$pos . "_percent"] = round($posMatrix[$pos] / $sum, 2);
            }
            $posMatrix["og_x"] = $posMatrix["og_y"] = $base * (1 - $posMatrix["og_percent"]);

            $posMatrix["oo_x"] = $base * (1 - $posMatrix["oo_percent"]);
            $posMatrix["oo_y"] = $base * ($posMatrix["oo_percent"]) + $base;

            $posMatrix["co_x"] = $posMatrix["co_y"] = $base * ($posMatrix["co_percent"]) + $base;

            $posMatrix["cg_x"] = $base * ($posMatrix["cg_percent"]) + $base;
            $posMatrix["cg_y"] = $base * (1 - $posMatrix["cg_percent"]);
        }

        $poly = Html::tag("polygon", null, [
            "points" =>
                $posMatrix["og_x"] . "," . $posMatrix["og_y"] . " " .
                $posMatrix["oo_x"] . "," . $posMatrix["oo_y"] . " " .
                $posMatrix["co_x"] . "," . $posMatrix["co_y"] . " " .
                $posMatrix["cg_x"] . "," . $posMatrix["cg_y"],
            "style" => "fill:$color"
        ]);

        $horizon = Html::tag("line", null, [
            "x1" => 0,
            "y1" => $size / 2,
            "x2" => $size,
            "y2" => $size / 2,
            "style" => "stroke:$gray; stroke-width:1",
        ]);
        $vertik = Html::tag("line", null, [
            "x1" => $size / 2,
            "y1" => 0,
            "x2" => $size / 2,
            "y2" => $size,
            "style" => "stroke:$gray;stroke-width:1",
        ]);
        $quarter = $size / 4;
        $medium = Html::tag("polygon", null, [
            "points" => $quarter . "," . $quarter . " " .
                $quarter . "," . (3 * $quarter) . ", " .
                (3 * $quarter) . "," . (3 * $quarter) . " " .
                (3 * $quarter) . "," . $quarter,
            "style" => "fill:transparent; stroke:$gray; stroke-width:1"
        ]);
        $out = Html::tag("polygon", null, [
            "points" => "0,0 " .
                "0,$size " .
                "$size,$size " .
                "$size,0",
            "style" => "fill:transparent; stroke:$gray; stroke-width:1"
        ]);

        return Html::tag("svg", $poly . $horizon . $vertik . $medium . $out, ["viewBox" => "0 0 $size $size", "width" => $size, "height" => $size]);
    }
}
