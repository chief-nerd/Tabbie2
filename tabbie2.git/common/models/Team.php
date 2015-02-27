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
 * @property DrawPosition[] $drawPositions
 * @property InSociety $inSocieties
 * @property Adjudicator[] $adjudicators
 * @property Tournament $tournament
 * @property User $speakerA
 * @property User $speakerB
 */
class Team extends \yii\db\ActiveRecord {

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
    public function getDrawPositions() {
        return $this->hasMany(DrawPosition::className(), ['team_id' => 'id']);
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

    /*
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
            $drawObject = DrawAfterRound::findOne(["round_id" => $round->id]);
            if ($drawObject instanceof DrawAfterRound)
                return $drawObject->getTeamPoints($this->id);
            else
                return 0; //No Round yet
        } else {
            throw new Exception("No Round found when getting Points");
        }
    }

    public function getPreviousPositionMatrix() {
        return [
            "OG" => 0,
            "OO" => 0,
            "CO" => 0,
            "OO" => 0,
        ];
    }

    /**
     * Sort function based on team points
     * @param Team $a
     * @param Team $b
     */
    public static function sort_points($a, $b) {
        $ap = $a->getPoints();
        $bp = $b->getPoints();
        return ($ap < $bp) ? 1 : (($ap > $bp) ? -1 : 0);
    }

}
