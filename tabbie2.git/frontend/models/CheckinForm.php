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

	public function save() {
		$messages = [];
		$type = substr($this->number, 0, 1);
		$real = substr($this->number, 1, strlen($this->number));

		switch ($type) {
			case 1: //Adjudicator
				$adj = Adjudicator::findOne($real);
				if ($adj instanceof Adjudicator) {
					$adj->checkedin = true;
					$adj->save();
					$messages[] = ["success" => $adj->name . " checked in!"];
				}
				else
					$messages[] = ["danger" => $real . " Number not valid! Not an Adjudicator!"];

				break;

			case 2: //Team A
				$team = Team::findOne($real);
				if ($team instanceof Team) {
					$team->speakerA_checkedin = true;
					$team->save();
					$messages[] = ["success" => $team->speakerA->name . " checked in!"];
				}
				else
					$messages[] = ["danger" => $real . " Number not valid! Not a Team A"];

				break;

			case 3: //Team B
				$team = Team::findOne($real);
				if ($team instanceof Team) {
					$team->speakerB_checkedin = true;
					$team->save();
					$messages[] = ["success" => $team->speakerB->name . " checked in!"];
				}
				else
					$messages[] = ["danger" => $real . " Number not valid! Not a Team B"];

				break;

			default:
				$messages[] = ["danger" => "Not a valid input"];
				break;
		}
		return $messages;
	}

}
