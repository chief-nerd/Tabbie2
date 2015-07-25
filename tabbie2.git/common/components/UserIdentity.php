<?php

namespace common\components;

use common\models\Adjudicator;
use common\models\Feedback;
use common\models\LanguageOfficer;
use common\models\Panel;
use common\models\Team;
use \common\models\Tournament;
use \common\models\User;
use common\models\Debate;
use yii\base\Exception;
use Yii;

class UserIdentity extends \yii\web\User
{

	public static function className()
	{
		return "common\components\UserIdentity";
	}

	/**
	 * Check if the user is Admin
	 *
	 * @return boolean
	 */
	public function isAdmin()
	{
		$user = $this->getModel();
		if ($user instanceof User && $user->role == User::ROLE_ADMIN) {
			return true;
		}

		return false;
	}

	/**
	 * @param Round $lastRound
	 *
	 * @return boolean
	 */
	public function hasChairedLastRound($info)
	{
		if ($info['type'] == 'judge' && $info['pos'] == 1) {
			return true;
		}

		return false;
	}

	/**
	 * @param Round $lastRound
	 *
	 * @return Debate
	 */
	public function hasOpenFeedback($info)
	{

		$debate = $info['debate'];
		if ($debate && $this->id > 0) {
			/** check teams* */
			if ($debate->og_feedback == 0 && $debate->isOGTeamMember($this->id))
				return ["type" => Feedback::FROM_TEAM, "id" => $debate->id, "ref" => $debate->og_team_id];
			if ($debate->oo_feedback == 0 && $debate->isOOTeamMember($this->id))
				return ["type" => Feedback::FROM_TEAM, "id" => $debate->id, "ref" => $debate->oo_team_id];
			if ($debate->cg_feedback == 0 && $debate->isCGTeamMember($this->id))
				return ["type" => Feedback::FROM_TEAM, "id" => $debate->id, "ref" => $debate->cg_team_id];
			if ($debate->co_feedback == 0 && $debate->isCOTeamMember($this->id))
				return ["type" => Feedback::FROM_TEAM, "id" => $debate->id, "ref" => $debate->co_team_id];

			/** check judges * */
			foreach ($debate->panel->adjudicatorInPanels as $judge) {
				if ($judge->got_feedback == 0 && $judge->adjudicator->user_id == $this->id) {
					if ($judge->function == Panel::FUNCTION_CHAIR)
						$type = Feedback::FROM_CHAIR;
					else
						$type = Feedback::FROM_WING;

					return ["type" => $type, "id" => $debate->id, "ref" => $judge->adjudicator->id];
				}
			}
		}

		return false;
	}

	/**
	 * Get the full User Model
	 *
	 * @return \common\models\User
	 */
	public function getModel()
	{
		return $user = User::findOne($this->id);
	}

	public function getRoleModel($tid)
	{
		$adj = \common\models\Adjudicator::find()->where(["tournament_id" => $tid, "user_id" => $this->id])->one();
		if ($adj instanceof \common\models\Adjudicator)
			return $adj;
		else {
			$team = \common\models\Team::find()
				->where("tournament_id = :tid AND (speakerA_id = :uid OR speakerB_id = :uid)", [
					":tid" => $tid,
					":uid" => $this->id,
				])
				->one();
			if ($team instanceof \common\models\Team)
				return $team;
		}

		return null;
	}

}
