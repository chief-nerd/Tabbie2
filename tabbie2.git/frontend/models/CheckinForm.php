<?php

namespace frontend\models;

use common\models\Adjudicator;
use common\models\Team;
use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class CheckinForm extends Model {

	const ADJU  = "AA";
	const TEAMA = "TA";
	const TEAMB = "TB";

	public $number;
	public $key;

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			// name, email, subject and body are required
			[['number'], 'required'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'number' => 'Barcode',
			'key' => 'Security Key',
		];
	}

	public static function generateKey($id, $tournament) {
		$type = 0;
		if (Yii::$app->user->isAdjudicator($tournament))
			$type = self::ADJU;
		elseif (Yii::$app->user->isTeamA($tournament))
			$type = self::TEAMA;
		elseif (Yii::$app->user->isTeamB($tournament))
			$type = self::TEAMB;

		return $type . '-' . $id;
	}

	public function save() {
		$messages = [];
		$type = substr($this->number, 0, 2);
		$real = substr($this->number, 3, strlen($this->number));

		switch ($type) {
			case self::ADJU: //Adjudicator
				$adj = Adjudicator::findOne($real);
				if ($adj instanceof Adjudicator) {
					$adj->checkedin = true;
					$adj->save();
					$messages[] = ["success" => Yii::t("app", "{adju} checked in!", ["adju" => $adj->name])];
				}
				else
					$messages[] = ["danger" => Yii::t("app", "{id} number not valid! Not an Adjudicator!", ["id" => $real])];

				break;

			case self::TEAMA: //Team A
				$team = Team::findOne($real);
				if ($team instanceof Team) {
					$team->speakerA_checkedin = true;
					$team->save();
					$messages[] = ["success" => Yii::t("app", "{speaker} checked in!", ["speaker" => $team->speakerA->name])];
				}
				else
					$messages[] = ["danger" => Yii::t("app", "{id} number not valid! Not a Team!", ["id" => $real])];

				break;

			case self::TEAMB: //Team B
				$team = Team::findOne($real);
				if ($team instanceof Team) {
					$team->speakerB_checkedin = true;
					$team->save();
					$messages[] = ["success" => Yii::t("app", "{speaker} checked in!", ["speaker" => $team->speakerB->name])];
				}
				else
					$messages[] = ["danger" => Yii::t("app", "{id} number not valid! Not a Team!", ["id" => $real])];

				break;

			default:
				$messages[] = ["danger" => Yii::t("app", "Not a valid input")];
				break;
		}
		return $messages;
	}

}
