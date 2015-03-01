<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "team".
 *
 * @property integer $id
 * @property string $name
 * @property integer $tournament_id
 * @property integer $speakerA_id
 * @property integer $speakerB_id
 * @property integer $society_id
 *
 * @property TabPosition[] $tabPositions
 * @property InSociety $inSocieties
 * @property Adjudicator[] $adjudicators
 * @property Tournament $tournament
 * @property User $speakerA
 * @property User $speakerB
 */
class Team extends \yii\db\ActiveRecord {

    const OG = 0;
    const OO = 1;
    const CG = 2;
    const CO = 3;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'team';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['tournament_id', 'name', 'speakerA_id', 'speakerB_id', 'society_id'], 'required'],
            [['tournament_id', 'speakerA_id', 'speakerB_id', 'society_id'], 'integer'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Team Name'),
            'tournament_id' => Yii::t('app', 'Tournament ID'),
            'speakerName' => Yii::t('app', 'Speaker Name'),
            'speakerA_id' => Yii::t('app', 'Speaker A'),
            'speakerB_id' => Yii::t('app', 'Speaker B'),
            'societyName' => Yii::t('app', 'Society Name'),
            'society_id' => Yii::t('app', 'Society'),
        ];
    }

    public function getSocietyName() {
        return $this->society->fullname;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTabPositions() {
        return $this->hasMany(TabPosition::className(), ['team_id' => 'id']);
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
    public function getSpeakerA() {
        return $this->hasOne(User::className(), ['id' => 'speakerA_id'])->from('user uA');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpeakerB() {
        return $this->hasOne(User::className(), ['id' => 'speakerB_id'])->from('user uB');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInSocieties() {
        return InSociety::find()->where("user_id IN (:userA, :userB) AND ending is null", [
                    ":userA" => $this->speakerA_id,
                    ":userB" => $this->speakerB_id,
        ]);
    }

    public function getSociety() {
        return $this->hasOne(Society::className(), ['id' => 'society_id']);
    }

    /**
     * Returns the points the team is on at the CURRENT state of the tournament
     * @use getPointsAfterRound
     * @return int
     */
    public function getPoints() {
        $last_round = $this->tournament->getLastRound();
        return $this->getPointsAfterRound($last_round->number);
    }

    /**
     * Get the points the team is on after the specified round.
     * @param integer $number
     * @return int
     */
    public function getPointsAfterRound($number) {
        $round = Round::find()->where([
                    "number" => $number,
                    "tournament_id" => $this->tournament_id
                ])->one();

        if ($round instanceof Round) {
            $tabObject = TabAfterRound::findOne(["round_id" => $round->id]);
            if ($tabObject instanceof TabAfterRound)
                return $tabObject->getTeamPoints($this->id);
            else
                return 0; //No Round yet
        } else {
            throw new Exception("No Round found when getting Points");
        }
    }

    /**
     * Sort comparison function based on team points
     * @param Team $a
     * @param Team $b
     */
    public static function compare_points($a, $b) {
        $ap = $a->getPoints();
        $bp = $b->getPoints();
        return ($ap < $bp) ? 1 : (($ap > $bp) ? -1 : 0);
    }

    /**
     * Helper function to determine whether teams COULD replace each other in the same bracket (are they in the same bracket, or is one a pull up / down from their bracket?)
     * Debate level = hightest points of teams
     * @param Team $other_team
     * @uses Team::getPoints
     * @uses Team::getLevel
     * @return bool
     */
    public function is_swappable_with($other_team) {
        $result = ($this->id != $other_team->id) &&
                (($this->points == $other_team->points) ||
                ($this->level == $other_team->level));
        return $result;
    }

    /**
     * The Position Badness Lookup table
     * @todo make that dynamic, bitch!
     * @return array
     */
    public static function PositionBadnessTable() {
        return array(
            "0, 0, 0, 0" => 0,
            "0, 0, 0, 1" => 0,
            "0, 0, 0, 2" => 4,
            "0, 0, 0, 3" => 36,
            "0, 0, 0, 4" => 144,
            "0, 0, 0, 5" => 324,
            "0, 0, 0, 6" => 676,
            "0, 0, 0, 7" => 1296,
            "0, 0, 0, 8" => 2304,
            "0, 0, 0, 9" => 3600,
            "0, 0, 1, 1" => 0,
            "0, 0, 1, 2" => 4,
            "0, 0, 1, 3" => 36,
            "0, 0, 1, 4" => 100,
            "0, 0, 1, 5" => 256,
            "0, 0, 1, 6" => 576,
            "0, 0, 1, 7" => 1156,
            "0, 0, 1, 8" => 1936,
            "0, 0, 2, 2" => 16,
            "0, 0, 2, 3" => 36,
            "0, 0, 2, 4" => 100,
            "0, 0, 2, 5" => 256,
            "0, 0, 2, 6" => 576,
            "0, 0, 2, 7" => 1024,
            "0, 0, 3, 3" => 64,
            "0, 0, 3, 4" => 144,
            "0, 0, 3, 5" => 324,
            "0, 0, 3, 6" => 576,
            "0, 0, 4, 4" => 256,
            "0, 0, 4, 5" => 400,
            "0, 1, 1, 1" => 0,
            "0, 1, 1, 2" => 4,
            "0, 1, 1, 3" => 16,
            "0, 1, 1, 4" => 64,
            "0, 1, 1, 5" => 196,
            "0, 1, 1, 6" => 484,
            "0, 1, 1, 7" => 900,
            "0, 1, 2, 2" => 4,
            "0, 1, 2, 3" => 16,
            "0, 1, 2, 4" => 64,
            "0, 1, 2, 5" => 196,
            "0, 1, 2, 6" => 400,
            "0, 1, 3, 3" => 36,
            "0, 1, 3, 4" => 100,
            "0, 1, 3, 5" => 196,
            "0, 1, 4, 4" => 144,
            "0, 2, 2, 2" => 4,
            "0, 2, 2, 3" => 16,
            "0, 2, 2, 4" => 64,
            "0, 2, 2, 5" => 144,
            "0, 2, 3, 3" => 36,
            "0, 2, 3, 4" => 64,
            "0, 3, 3, 3" => 36,
            "1, 1, 1, 1" => 0,
            "1, 1, 1, 2" => 0,
            "1, 1, 1, 3" => 4,
            "1, 1, 1, 4" => 36,
            "1, 1, 1, 5" => 144,
            "1, 1, 1, 6" => 324,
            "1, 1, 2, 2" => 0,
            "1, 1, 2, 3" => 4,
            "1, 1, 2, 4" => 36,
            "1, 1, 2, 5" => 100,
            "1, 1, 3, 3" => 16,
            "1, 1, 3, 4" => 36,
            "1, 2, 2, 2" => 0,
            "1, 2, 2, 3" => 4,
            "1, 2, 2, 4" => 16,
            "1, 2, 3, 3" => 4,
            "2, 2, 2, 2" => 0,
            "2, 2, 2, 3" => 0
        );
    }

    /**
     * Gets an integer value representing how BAD the current position is for the Team
     * @param integer $pos
     * @return integer
     */
    public function getPositionBadness($pos) {

        $positions = $this->getPastPositionMatrix();
        $badness_lookup = Team::PositionBadnessTable();

        $positions[$pos] += 1;
        sort($positions);

        while (($positions[0] + $positions[1] + $positions[2] + $positions[3]) >= 10) {
            for ($i = 0; $i < 4; $i++)
                $positions[$i] = max(0, $positions[$i] - 1);
        }
        return $badness_lookup["{$positions[0]}, {$positions[1]}, {$positions[2]}, {$positions[3]}"];
    }

    /**
     * Return the previous PositionMatrix the Team has been in to
     * 0 => OG,
     * 1 => OO,
     * 2 => CG,
     * 3 => CO,
     * @return array[4]
     */
    public function getPastPositionMatrix() {

        $og = Debate::find()->where(["tournament_id" => $this->tournament_id, "og_team_id" => $this->id])->count();
        $oo = Debate::find()->where(["tournament_id" => $this->tournament_id, "oo_team_id" => $this->id])->count();
        $cg = Debate::find()->where(["tournament_id" => $this->tournament_id, "cg_team_id" => $this->id])->count();
        $co = Debate::find()->where(["tournament_id" => $this->tournament_id, "co_team_id" => $this->id])->count();

        return [$og, $oo, $cg, $co];
    }

}
