<?php

namespace frontend\models;

use common\models\Adjudicator;
use common\models\Country;
use common\models\InSociety;
use common\models\Society;
use common\models\Team;
use common\models\User;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * ContactForm is the model behind the contact form.
 */
class DebregsyncForm extends Model {

	const ADJU = "adjudicators";
	const TEAM = "teams";

	const TYPE_SOC  = 1;
	const TYPE_USER = 2;
	const TYPE_TEAM = 3;
	const TYPE_ADJU = 4;

	/**
	 * @var
	 */
	public $url;
	public $key;
	public $tournament;

	private $FIX = [];

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			// name, email, subject and body are required
			[['url', 'key'], 'required'],
			[['tournament'], 'safe'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'url' => Yii::t("app", 'DebReg URL'),
		];
	}

	public function doSync($a_fix, $t_fix, $s_fix) {

		$this->FIX = [
			"a" => $a_fix,
			"t" => $t_fix,
			"s" => $s_fix,
		];

		$this->syncAdjudicator();
		$this->syncTeams();

		if (count($this->FIX['a']) > 0 || count($this->FIX['t']) > 0 || count($this->FIX['s']) > 0)
			return [
				"a_fix" => $this->FIX['a'],
				"t_fix" => $this->FIX['t'],
				"s_fix" => $this->FIX['s']
			];


		return [];
	}

	private function readData($url, $object, $key = null) {

		$data = ["key" => $key];
		$data = http_build_query($data);

		$context = stream_context_create([
			'http' => array(
				'method' => 'GET',
				'Header' => 'Content-type: application/x-www-form-urlencoded\r\n' .
					'Content-Length: ' . strlen($data) . '\r\n',
				'Content' => $data
			)
		]);
		$json_string = @file_get_contents($url . "/" . $object);

		if (strlen($json_string) == 0) throw new Exception("No content received for " . $object, 404);

		$json = json_decode($json_string);

		if ($json->status != 200) throw new Exception("No successful data received. Error: " . $json->error_msg, $json->status);

		return $json;
	}

	private function syncTeams() {
		try {
			$json = $this->readData($this->url, self::TEAM);

			$oldTeams = Team::find()
			                ->tournament($this->tournament->id)
			                ->asArray()
			                ->all();

			Team::deleteAll(["tournament_id" => $this->tournament->id]);

			$count = count($json->items);

			for ($i = 0; $i < $count; $i++) {
				$item = $json->items[$i];

				$society = $this->handleSociety($item);

				/*** A ***/
				/** @var User $userA */
				$userA = User::find()
				             ->andWhere(["CONCAT(user.givenname,' ',user.surename)" => $item->speaker[0]->firstname . " " . $item->speaker[0]->lastname])
				             ->orWhere(["email" => $item->speaker[0]->email])
				             ->all();

				if (count($userA) > 1) {
					$name = $item->speaker[0]->firstname . " " . $item->speaker[0]->lastname;
					if (isset($this->FIX['t'][$name])) {
						$userA = User::findOne($this->FIX['t'][$name]);
						if ($userA instanceof User)
							unset($this->FIX['t'][$name]);
						else
							throw new Exception("User A no resolved");
					}
					else {

						$soc = (isset($society->fullname)) ? $society->fullname : $item->society->name;

						$matches = [];
						foreach ($userA as $match) {
							$matches[$match->id] = $match->givenname . " " . $match->surename . " (" . $soc . ")";
						}
						$this->FIX['t'][$item->speaker[0]->firstname . " " . $item->speaker[0]->lastname] = [
							"key" => $item->speaker[0]->firstname . " " . $item->speaker[0]->lastname,
							"matches" => $matches,
							"msg" => "Multiple user matches for " . $item->speaker[0]->firstname . " " . $item->speaker[0]->lastname . " (" . $item->society->name . ")"
						];
					}
				}
				else {
					if (count($userA) == 0) {
						$userA = User::NewViaImport($item->speaker[0]->firstname, $item->speaker[0]->lastname, $item->speaker[0]->email, $society->id);
					}
					else {
						$userA = $userA[0];
					}
				}

				/*** B ***/
				/** @var User $userB */
				$userB = User::find()
				             ->andWhere(["CONCAT(user.givenname,' ',user.surename)" => $item->speaker[1]->firstname . " " . $item->speaker[1]->lastname])
				             ->orWhere(["email" => $item->speaker[1]->email])
				             ->all();

				if (count($userB) > 1) {
					$name = $item->speaker[1]->firstname . " " . $item->speaker[1]->lastname;
					if (isset($this->FIX['t'][$name])) {
						$user = User::findOne($this->FIX['t'][$name]);
						if ($user instanceof User)
							unset($this->FIX['t'][$name]);
						else
							throw new Exception("User B no resolved");
					}
					else {

						$soc = (isset($society->fullname)) ? $society->fullname : $item->society->name;

						$matches = [];
						foreach ($userB as $match) {
							$matches[$match->id] = $match->givenname . " " . $match->surename . " (" . $soc . ")";
						}
						$this->FIX['t'][$item->speaker[1]->firstname . " " . $item->speaker[1]->lastname] = [
							"key" => $item->speaker[1]->firstname . " " . $item->speaker[1]->lastname,
							"matches" => $matches,
							"msg" => "Multiple user matches for " . $item->speaker[1]->firstname . " " . $item->speaker[1]->lastname . " (" . $item->society->name . ")"
						];
					}
				}
				else {
					if (count($userB) == 0) {
						$userB = User::NewViaImport($item->speaker[1]->firstname, $item->speaker[1]->lastname, $item->speaker[1]->email, $society->id);
					}
					else {
						$userB = $userB[0];
					}
				}

				if (!is_array($userA) && !is_array($userB) && $userA instanceof User && $userB instanceof User) {
					$team = null;

					if ($old = $this->helperGetTeam($oldTeams, $userA->id, $userB->id)) {
						$team = new Team($old);
					}
					else if ($society instanceof Society) {
						$team = new Team([
							"tournament_id" => $this->tournament->id,
							"name" => $item->name,
							"speakerA_id" => $userA->id,
							"speakerB_id" => $userB->id,
							"society_id" => $society->id,
						]);
					}

					if ($team instanceof Team && !$team->save()) {
						throw new Exception("Can't save Team. " . print_r($team->attributes, true));
					}
				}
			}

			return true;

		} catch (Exception $ex) {
			Yii::$app->session->addFlash("error", $ex->getMessage() . " on " . __FUNCTION__ . ":" . $ex->getLine());
		}
	}

	private function syncAdjudicator() {
		try {
			$unresolved = [];
			$json = $this->readData($this->url, self::ADJU);
			$count = count($json->items);

			$oldAdjus = Adjudicator::find()
			                       ->tournament($this->tournament->id)
			                       ->asArray()
			                       ->all();

			Adjudicator::deleteAll(["tournament_id" => $this->tournament->id]);


			for ($i = 0; $i < $count; $i++) {
				$item = $json->items[$i];
				$item->name = $item->firstname . " " . $item->lastname;

				$society = $this->handleSociety($item);

				$user = User::find()
				            ->andWhere(["CONCAT(user.givenname,' ',user.surename)" => $item->name])
				            ->orWhere(["email" => $item->email])
				            ->all();

				if (count($user) > 1) {
					if (isset($this->FIX['a'][$item->name])) {
						$user = User::findOne($this->FIX['a'][$item->name]);
						if ($user instanceof User)
							unset($this->FIX['a'][$item->name]);
						else
							throw new Exception("User B no resolved");
					}
					else {
						$matches = [];
						foreach ($user as $match) {
							$matches[$match->id] = $match->givenname . " " . $match->surename . " (" . $match->email . ")";
						}
						$this->FIX['a'][$item->name] = [
							"key" => $item->name,
							"msg" => "Multiple user found! Select the right " . $item->name,
							"matches" => $matches,
						];
					}
				}
				else {
					if (count($user) == 0) {
						$user = User::NewViaImport($item->firstname, $item->lastname, $item->email, $society->id);
					}
					else {
						$user = $user[0];
					}
				}

				if (!is_array($user) && $user instanceof User) {
					if ($old = $this->helperGetAdju($oldAdjus, $user->id)) {
						$adju = new Adjudicator($old);
					}
					else {
						$adju = new Adjudicator([
							"tournament_id" => $this->tournament->id,
							"user_id" => $user->id,
							"society_id" => $society->id,
							"strength" => 0,
						]);
					}

					if (!$adju->save()) {
						throw new Exception("Error saving Adjudicator. " . print_r($adju->getErrors(), true));
					}
				}
			}
			return true;

		} catch
		(Exception $ex) {
			Yii::$app->session->addFlash("error", $ex->getMessage() . " on " . __FUNCTION__ . ":" . $ex->getLine());
		}
	}

	private function helperGetAdju($array, $id) {
		$c = count($array);
		for ($i = 0; $i < $c; $i++) {
			if ($array[$i]["user_id"] == $id) return $array[$i];
		}
		return false;
	}

	private function helperGetTeam($array, $Aid, $Bid) {
		$c = count($array);
		for ($i = 0; $i < $c; $i++) {
			if ($array[$i]["speakerA_id"] == $Aid && $array[$i]["speakerB_id"] == $Bid) return $array[$i];
		}
		return false;
	}

	private function handleSociety($item) {
		$societies = Society::find()->where(["fullname" => $item->society->name])->all();

		if (count($societies) == 0) {
			$country = Country::find()->where(["like", "name", $item->society->country])->one();

			$society = new Society([
				"fullname" => $item->society->name,
				"abr" => (strlen($item->society->abr) > 0) ? $item->society->abr : Society::generateAbr($item->society->name),
				"city" => (strlen($item->society->city) > 0) ? $item->society->city : null,
				"country_id" => ($country instanceof Country) ? $country->id : Country::COUNTRY_UNKNOWN_ID,
			]);
			if (!$society->save())
				throw new Exception("Cant save new Society error: " . print_r($society->getErrors(), true));

			return $society;

		}
		elseif (count($societies) == 1 && $societies[0] instanceof Society) {
			return $societies[0];
		}
		else {
			//Do we find a FIX?
			if (isset($this->FIX['s'][$item->society->name])) {
				$society = Society::findOne($this->FIX['s'][$item->society->name]); //this has the ID
				if ($society instanceof Society) {
					unset($this->FIX['s'][$item->society->name]); //Fixed
					return $society;
				}
			}
			else {
				$matches = [];
				foreach ($societies as $match) {
					$matches[$match->id] = $match->fullname . " (" . $match->abr . ")";
				}
				$this->FIX['s'][$item->society->name] = [
					"key" => $item->society->name,
					"msg" => "Multiple society found! Select the right " . $item->society->name,
					"matches" => $matches,
				];
			}
		}


		return false;
	}
}
