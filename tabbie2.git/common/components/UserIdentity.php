<?php

namespace common\components;

use \common\models\Tournament;
use \common\models\User;

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
     * Get the full User Model
     * @return \common\models\User
     */
    public function getModel() {
        return $user = User::findOne($this->id);
    }

}
