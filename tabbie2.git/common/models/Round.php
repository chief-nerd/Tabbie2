<?php

namespace common\models;

use Yii;
use yii\base\Exception;

/**
 * This is the model class for table "round".
 *
 * @property integer $id
 * @property integer $tournament_id
 * @property string $motion
 * @property string $infoslide
 * @property string $time
 * @property bool $published
 * @property bool $displayed
 * @property bool $closed
 * @property datetime $prep_started
 * @property datetime $finished_time
 *
 * @property DrawAfterRound[] $drawAfterRounds
 * @property Tournament $tournament
 */
class Round extends \yii\db\ActiveRecord {

    const STATUS_CREATED = 0;
    const STATUS_PUBLISHED = 1;
    const STATUS_DISPLAYED = 2;
    const STATUS_STARTED = 3;
    const STATUS_JUDGING = 4;
    const STATUS_CLOSED = 5;

    static function statusLabel($code = null) {

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

    public function getStatus() {

        if ($this->hasAllResultsEntered())
            return Round::STATUS_CLOSED;
        else if ($this->isJudgingTime())
            return Round::STATUS_JUDGING;
        else if ($this->isStartingTime())
            return Round::STATUS_STARTED;
        else if ($this->displayed == 1)
            return Round::STATUS_DISPLAYED;
        else if ($this->published == 1)
            return Round::STATUS_PUBLISHED;
        else if ($this->time)
            return Round::STATUS_CREATED;
        else
            throw new Exception("Unknow Round status for Round" . $this->id . " No create time");
    }

    public function isJudgingTime() {
        $debatetime = (8 * 7) + 8;
        $preptime = 15;
        if ($this->prep_started) {
            $judgeTime = strtotime($this->prep_started) + $preptime + $debatetime;

            if (time() > $judgeTime)
                return true;
        }
        return false;
    }

    public function isStartingTime() {
        $preptime = 15;
        if ($this->prep_started) {
            $prepende = strtotime($this->prep_started) + $preptime;

            if (time() > $prepende)
                return true;
        }
        return false;
    }

    public function hasAllResultsEntered() {
        return false;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'round';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'tournament_id', 'motion'], 'required'],
            [['id', 'tournament_id', 'published'], 'integer'],
            [['motion', 'infoslide'], 'string'],
            [['time'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'Round Number'),
            'tournament_id' => Yii::t('app', 'Tournament ID'),
            'motion' => Yii::t('app', 'Motion'),
            'infoslide' => Yii::t('app', 'Info Slide'),
            'time' => Yii::t('app', 'Time'),
            'published' => Yii::t('app', 'Published'),
            'displayed' => Yii::t('app', 'Displayed'),
            'prep_started' => Yii::t('app', 'PrepTime started'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDrawAfterRounds() {
        return $this->hasMany(DrawAfterRound::className(), ['round_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTournament() {
        return $this->hasOne(Tournament::className(), ['id' => 'tournament_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDebates() {
        return $this->hasMany(Debate::className(), ['round_id' => 'id', 'tournament_id' => 'tournament_id']);
    }

    /**
     * Generate a draw for the model
     */
    public function generateDraw() {
        try {
            $algoName = "common\components\TabAlgorithmus\\" . "DummyTest";
            $algo = new $algoName();
            $venues = Venue::find()->active()->tournament($this->tournament->id)->all();
            $draw = $algo->makeDraw($venues, $this->tournament->teams, $this->tournament->adjudicators);

            foreach ($draw as $line) {

                if (!isset($line["panel"]["id"])) {
                    $panel = new Panel();
                    $panel->tournament_id = $this->tournament_id;

                    //Set Strength if available
                    if (isset($line["panel"]["strength"])) {
                        $panel->strength = $line["panel"]["strength"];
                        unset($line["panel"]["strength"]);
                    } else {
                        $panel->strength = 0;
                    }
                    //Save Panel
                    if (!$panel->save())
                        throw new Exception("Can't save Panel " . print_r($panel->getErrors(), true));

                    $panelID = $panel->id;

                    foreach ($line["panel"] as $type => $judge) {
                        $alloc = new AdjudicatorInPanel();
                        $alloc->adjudicator_id = $judge->id;
                        $alloc->panel_id = $panelID;
                        if ($type === "chair")
                            $alloc->function = Panel::FUNCTION_CHAIR;
                        else
                            $alloc->function = Panel::FUNCTION_WING;
                        if (!$alloc->save())
                            throw new Exception("Can't save AdjudicatorInPanel " . print_r($alloc->getErrors(), true));
                    }
                } else
                    $panelID = $line["panel"]["id"];

                $debate = new Debate();
                $debate->round_id = $this->id;
                $debate->tournament_id = $this->tournament_id;
                $debate->og_team_id = $line["og"]->id;
                $debate->oo_team_id = $line["oo"]->id;
                $debate->cg_team_id = $line["cg"]->id;
                $debate->co_team_id = $line["co"]->id;
                $debate->venue_id = $line["venue"]->id;
                $debate->panel_id = $panelID;
                if (!$debate->save())
                    throw new Exception("Can't save Debate " . print_r($debate->getErrors(), true));
            }
            return true;
        } catch (Exception $ex) {
            $this->addError("TabAlgorithmus", $ex->getMessage());
        }
        return false;
    }

}
