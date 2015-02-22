<?php

namespace common\components;

use \common\models\Tournament;
use \common\models\User;
use common\models\Debate;

class UserIdentity extends \yii\web\User {

    public static function className() {
        return "common\components\UserIdentity";
    }

    /**
     * Check if user is the tabmaster of the torunament
     * @param int $tournament_id
     * @return boolean
     */
    public function isTabMaster($tournament) {
        if ($tournament instanceof Tournament && $tournament->tabmaster_user_id == $this->id) {
            \Yii::trace("User is Tab Master for Tournament #" . $tournament->id, __METHOD__);
            return true;
        }
        return false;
    }

    /**
     * Check if the user is Admin
     * @return boolean
     */
    public function isAdmin() {
        $user = $this->getModel();
        \Yii::trace("User has Role: " . $user->role, __METHOD__);
        if ($user->role == User::ROLE_ADMIN) {
            return true;
        }

        return false;
    }

    /**
     *
     * @param Tournament $model
     */
    public function hasChairedLastRound($model) {
        $lastRound = $model->getLastRound();
        if ($lastRound) {
            /* @var $debate Debate */
            foreach ($lastRound->getDebates()->all() as $debate) {
                if ($debate->getChair()->user_id == $this->id)
                    return $debate;
            }
        }
        return false;
    }

    /**
     *
     * @param Tournament $model
     */
    public function hasOpenFeedback($model) {
        $lastRound = $model->getLastRound();
        if ($lastRound) {
            /* @var $debate Debate */
            foreach ($lastRound->getDebates()->all() as $debate) {
                /** check teams* */
                if ($debate->og_feedback == 0 && $debate->isOGTeamMember($this->id))
                    return $debate;
                if ($debate->oo_feedback == 0 && $debate->isOOTeamMember($this->id))
                    return $debate;
                if ($debate->cg_feedback == 0 && $debate->isCGTeamMember($this->id))
                    return $debate;
                if ($debate->co_feedback == 0 && $debate->isCOTeamMember($this->id))
                    return $debate;

                /** check judges * */
                foreach ($debate->panel->adjudicatorInPanels as $judge) {
                    if ($judge->got_feedback == 0 && $judge->adjudicator->user_id == $this->id)
                        return $debate;
                }
            }
        }
        return false;
    }

    /**
     * Get the full User Model
     * @return \common\models\User
     */
    public function getModel() {
        return $user = User::findOne($this->id);
    }

    public function getRoleModel($tid) {
        $adj = \common\models\Adjudicator::find()->where(["tournament_id" => $tid, "user_id" => $this->id])->one();
        if ($adj instanceof \common\models\Adjudicator)
            return $adj;
        else {
            $team = \common\models\Team::find()->where("tournament_id = :tid AND (speakerA_id = :uid OR speakerB_id = :uid)", [
                        ":tid" => $tid,
                        ":uid" => $this->id,
                    ])->one();
            if ($team instanceof \common\models\Team)
                return $team;
        }
        return null;
    }

}
