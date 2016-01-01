<?php

namespace common\components;

use common\models\Adjudicator;
use common\models\Feedback;
use common\models\LanguageMaintainer;
use common\models\LanguageOfficer;
use common\models\Panel;
use common\models\Team;
use \common\models\Tournament;
use \common\models\User;
use common\models\Debate;
use yii\base\Exception;
use Yii;

class UserIdentity extends \yii\web\User {

	public static function className() {
		return "common\components\UserIdentity";
	}

	/**
	 * Check if the user is Admin
	 *
	 * @return boolean
	 */
	public function isAdmin() {
		$user = $this->getModel();
		if ($user instanceof User && $user->role == User::ROLE_ADMIN) {
			return true;
		}

		return false;
	}

	/**
	 * Get the full User Model
	 *
	 * @return \common\models\User
	 */
	public function getModel() {
		return User::findByPk($this->id);
	}

	public function isMaintainer() {
		$user = $this->getModel();
		if ($user instanceof User && ($user->role == User::ROLE_BACKEND || $user->role == User::ROLE_ADMIN)) {
			return true;
		}

		return false;
	}

	public function isLanguageMaintainer($lang = false) {
		$conditions = ["user_id" => $this->id];

		if ($lang != false) {
			$conditions["language_language"] = $lang;
		}

		$maintainer = LanguageMaintainer::findAll($conditions);

		if (count($maintainer) >= 1) {
			return true;
		}

		return false;
	}

	/**
	 * @param string[] $info
	 *
	 * @return boolean
	 */
	public function hasChairedLastRound($info) {
		if ($info['type'] == 'judge' && $info['pos'] == 1) {
			return true;
		}

		return false;
	}

	public function getRoleModel($tid) {
		$adj = \common\models\Adjudicator::find()->where(["tournament_id" => $tid, "user_id" => $this->id])->one();
		if ($adj instanceof \common\models\Adjudicator) {
			return $adj;
		} else {
			$team = \common\models\Team::find()
				->where("tournament_id = :tid AND (speakerA_id = :uid OR speakerB_id = :uid)", [
					":tid" => $tid,
					":uid" => $this->id,
				])
				->one();
			if ($team instanceof \common\models\Team) {
				return $team;
			}
		}

		return null;
	}

	public function getLanguage() {
		$user = $this->getModel();
		return (isset($user->language)) ? $user->language : "en-UK";
	}

}
