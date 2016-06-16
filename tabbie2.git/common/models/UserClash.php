<?php

namespace common\models;

use common\components\ObjectError;
use Yii;
use yii\base\Exception;
use yii\base\Object;

/**
 * This is the model class for table "user_clash".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $clash_with
 * @property string $reason
 * @property string $date
 *
 * @property AdjudicatorStrike[] $adjudicatorStrikes
 * @property TeamStrike[] $teamStrikes
 * @property User $user
 * @property User $clashWith
 */
class UserClash extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_clash';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'clash_with'], 'required'],
            [['user_id', 'clash_with'], 'integer'],
            [['date'], 'safe'],
            [['reason'], 'string', 'max' => 255],
            //[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['id']],
            //[['clash_with'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User') . ' ' . Yii::t('app', 'ID'),
            'clash_with' => Yii::t('app', 'Clash With'),
            'reason' => Yii::t('app', 'Reason'),
            'date' => Yii::t('app', 'Date'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdjudicatorStrikes()
    {
        return $this->hasMany(AdjudicatorStrike::className(), ['user_clash_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeamStrikes()
    {
        return $this->hasMany(TeamStrike::className(), ['user_clash_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClashWith()
    {
        return $this->hasOne(User::className(), ['id' => 'clash_with']);
    }

    public function getTypeLabel($tournament_id)
    {
        switch (get_class($this->getClashedObject($tournament_id))) {
            case Team::className():
                return Yii::t("app", "Team Clash");
            case Adjudicator::className():
                return Yii::t("app", "Adjudicator Clash");
            default:
                return Yii::t("app", "No type found");
        }
    }

    public function getClashedObject($tournament_id)
    {
        $a = Adjudicator::find()->tournament($tournament_id)->andWhere(["user_id" => $this->clash_with])->one();
        if ($a instanceof Adjudicator)
            return $a;
        else {
            $t = Team::find()->tournament($tournament_id)->andWhere("speakerA_id = :userA OR speakerB_id = :userB", [
                "userA" => $this->clash_with,
                "userB" => $this->clash_with
            ])->one();
            if ($t instanceof Team) {
                return $t;
            }
        }

        return new Object();
    }

    public function getOwnObject($tournament_id)
    {
        $a = Adjudicator::find()->tournament($tournament_id)->andWhere(["user_id" => $this->user_id])->one();
        if ($a instanceof Adjudicator)
            return $a;
        else {
            $t = Team::find()->tournament($tournament_id)->andWhere("speakerA_id = :user OR speakerB_id = :user", [
                "user" => $this->user_id
            ])->one();
            if ($t instanceof Team) {
                return $t;
            }
        }

        return new Object();
    }

    /**
     * Decide a user clash
     * @param $decision
     * @param $tournament_id
     *
     * @return bool
     * @throws Exception
     */
    public function decide($decision, $tournament_id)
    {
        $clashObj = $this->getClashedObject($tournament_id);
        $ownObj = $this->getOwnObject($tournament_id);
        $strike = false;
        if ($ownObj instanceof Adjudicator) {
            if ($clashObj instanceof Adjudicator) {
                //A->A
                $strike = new AdjudicatorStrike([
                    "adjudicator_from_id" => $ownObj->id,
                    "adjudicator_to_id" => $clashObj->id,
                    "tournament_id" => $tournament_id,
                    "user_clash_id" => $this->id,
                    "accepted" => $decision,
                ]);
            } else if ($clashObj instanceof Team) {
                //A-T
                $strike = new TeamStrike([
                    "team_id" => $clashObj->id,
                    "adjudicator_id" => $ownObj->id,
                    "tournament_id" => $tournament_id,
                    "user_clash_id" => $this->id,
                    "accepted" => $decision,
                ]);
            }
        } else if ($ownObj instanceof Team) {
            //only T->A
            $strike = new TeamStrike([
                "team_id" => $ownObj->id, //REVERSE!
                "adjudicator_id" => $clashObj->id, //REVERSE!
                "tournament_id" => $tournament_id,
                "user_clash_id" => $this->id,
                "accepted" => $decision,
            ]);
        } else {
            //T->T NOT possible
            throw new Exception("No Clash Combination found");
        }

        if (!$strike->save()) {
            throw new Exception(Yii::t("app", "Can't save clash decision. {reason}", [
                "reason" => ObjectError::getMsg($strike)
            ]));
        }

        return true;
    }

    /**
     * Get all UserClashes for a specific Tournament
     * @param $tournament_id
     *
     * @return UserClash[]
     */
    public static function getForTournament($tournament_id)
    {
        $clashes = [];

        $adjus = Adjudicator::find()->tournament($tournament_id)->all();
        foreach ($adjus as $j) {
            foreach ($j->user->clashes as $c) {
                $c_a = Adjudicator::find()
                    ->tournament($tournament_id)
                    ->andWhere(["user_id" => $c->clash_with])->one();

                if ($c_a instanceof Adjudicator) {
                    $already = AdjudicatorStrike::find()->where([
                        "tournament_id" => $tournament_id,
                        "adjudicator_from_id" => $j->id,
                        "adjudicator_to_id" => $c_a->id,
                    ])->exists();

                    if (!$already) {
                        $clashes[] = $c;
                    }

                } else { //No Adjudicator ... id might belong to a Team
                    $c_a = Team::find()
                        ->tournament($tournament_id)
                        ->andWhere("speakerA_id = :userA OR speakerB_id = :userB", [
                            "userA" => $c->clash_with,
                            "userB" => $c->clash_with,
                        ])->one();

                    if ($c_a instanceof Team) {
                        $already = TeamStrike::find()->where([
                            "tournament_id" => $tournament_id,
                            "team_id" => $c_a->id,
                            "adjudicator_id" => $j->id,
                        ])->exists();

                        if (!$already) {
                            $clashes[] = $c;
                        }
                    }
                }

            }
        }

        $team = Team::find()->tournament($tournament_id)->all();
        foreach ($team as $t) {
            if ($t->speakerA) {
                foreach ($t->speakerA->clashes as $c) {
                    $c_a = Adjudicator::find()
                        ->tournament($tournament_id)
                        ->andWhere(["user_id" => $c->clash_with])
                        ->one();

                    if ($c_a instanceof Adjudicator) {
                        $already = TeamStrike::find()->where([
                            "tournament_id" => $tournament_id,
                            "team_id" => $t->id,
                            "adjudicator_id" => $c_a->id,
                        ])->exists();

                        if (!$already) {
                            $clashes[] = $c;
                        }
                    } // No Team2Team Clash :D
                }
            }
            if ($t->speakerB) {
                foreach ($t->speakerB->clashes as $c) {
                    $c_a = Adjudicator::find()
                        ->tournament($tournament_id)
                        ->andWhere(["user_id" => $c->clash_with])
                        ->one();

                    if ($c_a instanceof Adjudicator) {
                        $already = TeamStrike::find()->where([
                            "tournament_id" => $tournament_id,
                            "team_id" => $t->id,
                            "adjudicator_id" => $c_a->id,
                        ])->exists();

                        if (!$already) {
                            $clashes[] = $c;
                        }
                    } // No Team2Team Clash :D
                }
            }
        }

        return $clashes;
    }
}
