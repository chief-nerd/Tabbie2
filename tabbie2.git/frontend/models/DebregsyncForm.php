<?php

namespace frontend\models;

use common\components\ObjectError;
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
class DebregsyncForm extends Model
{

	const ADJU = "Adjudicator";
	const TEAM = "Team";

	const TYPE_SOC = 1;
	const TYPE_USER = 2;
	const TYPE_TEAM = 3;
	const TYPE_ADJU = 4;


	const DEBREG_URL = "https://debreg-test.azurewebsites.net";
	/**
	 * @var
	 */
	public $debregId;
	public $key;
	public $username;
	public $password;
	public $tournament;

	private $FIX = [];

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			// name, email, subject and body are required
			[['debregId', 'username', 'password'], 'required'],
			[['tournament', 'key', 'debregId'], 'safe'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'debregId' => Yii::t("app", 'DebReg ID'),
		];
	}

	public function doSync($a_fix, $t_fix, $s_fix)
	{

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

	private function syncAdjudicator()
	{
		try {

			$json = $this->readData(self::ADJU, $this->key);
			$count = count($json);
			Yii::trace("$count lines of adjudicators received", __METHOD__);

			$oldAdjus = Adjudicator::find()
				->tournament($this->tournament->id)
				->asArray()
				->all();

			$deleted_adju = Adjudicator::deleteAll(["tournament_id" => $this->tournament->id]);
			$saved_adju = 0;
			$new_adju = 0;
			$old_adju = 0;

			for ($i = 0; $i < $count; $i++) {
				$item = $json[$i];

				$u = $item->user;

				$u_first = $u->firstname;
				$u_last = $u->lastname;
				$u_name = $u_first . " " . $u_last;
				$u_email = $u->eMail;


				$society = $this->handleSociety($item);

				$user = User::find()
					->andWhere(["CONCAT(user.givenname,' ',user.surename)" => $u_name])
					->orWhere(["email" => $u_email])
					->all();

				if (count($user) > 1) {
					if (isset($this->FIX['a'][$u_name])) {
						$user = User::findOne($this->FIX['a'][$u_name]);
						if ($user instanceof User)
							unset($this->FIX['a'][$u_name]);
						else
							throw new Exception("User no resolved");
					} else {
						$matches = [];
						foreach ($user as $match) {
							$matches[$match->id] = $match->givenname . " " . $match->surename . " (" . $match->email . ")";
						}
						$this->FIX['a'][$u_name] = [
							"key"     => $u_name,
							"msg"     => "Multiple user found! Select the right " . $u_name . " " . $u_email,
							"matches" => $matches,
						];
					}
				} else {
					if (count($user) == 0) {
						if ($society instanceof Society) {
							$user = User::NewViaImport($u_first, $u_last, $u_email, $society->id, true, $this->tournament);
							Yii::trace("New user created: $u_first $u_last, $u_email for $society->id", __METHOD__);
						} else {
							Yii::trace("No User + No Society\n" . print_r($item, true), __METHOD__);
							$user = null; //Error in Society -> no user yet to be known
						}
					} else { // must be == 1
						$user = $user[0];
					}
				}

				if (!is_array($user) && $user instanceof User) {
					$old = $this->helperGetAdju($oldAdjus, $user->id);
					if (is_array($old)) {
						$adju = new Adjudicator($old);
						$old_adju++;
					} else {
						$adju = new Adjudicator([
							"tournament_id" => $this->tournament->id,
							"user_id"       => $user->id,
							"society_id"    => $society->id,
							"strength"      => 0,
						]);
						$new_adju++;
					}

					if (!$adju->save()) {
						throw new Exception("Error saving Adjudicator: " . ObjectError::getMsg($adju));
					} else {
						$saved_adju++;
					}
				}
			}

			Yii::$app->session->addFlash("notice", "Adjudicator Deleted: $deleted_adju. Saved: $saved_adju. Old: $old_adju. New: $new_adju.");
			Yii::trace("DebReg Adju Import: Deleted: $deleted_adju. Saved: $saved_adju. Old: $old_adju. New: $new_adju.", __METHOD__);

			return true;

		} catch (Exception $ex) {
			Yii::$app->session->addFlash("error", $ex->getMessage() . " on " . __FUNCTION__ . ":" . $ex->getLine());
		}

		return false;
	}

	private function readData($object, $key = null)
	{
		if (strlen($object) == 0) throw new Exception("No Object");
		if (strlen($key) == 0) throw new Exception("No Key");

		$context = stream_context_create([
			'http' => [
				'method'        => 'GET',
				'header'        => [
					"Authorization: " . $key,
					"Accept: json",
				],
				'ignore_errors' => true,
			]
		]);

		if (strlen($this->debregId) == 0) throw new Exception("No Tournament");

		$url = self::DEBREG_URL . "/api/" . $object . "?tournamentId=" . $this->debregId;
		$json_string = @file_get_contents($url, false, $context);

		if (strlen($json_string) == 0) throw new Exception("No content received for " . $object, 404);

		$json = json_decode($json_string);

		if (isset($json->message)) throw new Exception($json->message);
		if (isset($json->Message)) throw new Exception($json->Message . "<br>\n" . $json->MessageDetail); //ROMAN!!! HATE HATE

		if (count($json) == 0) throw new Exception("No json data received");

		if (!is_array($json)) throw new Exception("Return Object not a JSON: " . $json_string);

		return $json;
	}

	private function handleSociety($item)
	{
		$org = $item->organization;
		$org_name = $org->name;
		$org_abr = $org->abbreviation;
		$org_city = $org->address->city;
		$org_country = $org->address->country;

		$societies = Society::find()->where(["fullname" => $org_name])->orWhere(["abr" => $org_abr])->all();

		if (count($societies) == 0) {

			if (strlen($org_country) > 0)
				$country = Country::find()->where(["like", "name", $org_country])->one();
			else
				$country = Country::COUNTRY_UNKNOWN_ID;

			$society = new Society([
				"fullname"   => $org_name,
				"abr"        => (strlen($org_abr) > 0) ? Society::uniqueAbr($org_abr) : Society::generateAbr($org_name),
				"city"       => (strlen($org_city) > 0) ? $org_city : null,
				"country_id" => ($country instanceof Country) ? $country->id : Country::COUNTRY_UNKNOWN_ID,
			]);
			if (!$society->save())
				throw new Exception("Cant save new Society error: " . print_r($society->getErrors(), true));

			return $society;

		} elseif (count($societies) == 1 && $societies[0] instanceof Society) {
			return $societies[0];
		} else {
			//Do we find a FIX?
			if (isset($this->FIX['s'][$org_name])) {
				$society = Society::findOne($this->FIX['s'][$org_name]); //this has the ID
				if ($society instanceof Society) {
					unset($this->FIX['s'][$org_name]); //Fixed
					return $society;
				}
			} else {
				$matches = [];
				foreach ($societies as $match) {
					$matches[$match->id] = $match->fullname . " (" . $match->abr . ")";
				}
				$this->FIX['s'][$org_name] = [
					"key"     => $org_name,
					"msg"     => "Select the right " . $org_name,
					"matches" => $matches,
				];
			}
		}


		return false;
	}

	private function helperGetAdju($array, $id)
	{
		$c = count($array);
		for ($i = 0; $i < $c; $i++) {
			if ($array[$i]["user_id"] == $id) return $array[$i];
		}

		return false;
	}

	private function syncTeams()
	{
		try {
			$json = $this->readData(self::TEAM, $this->key);

			$oldTeams = Team::find()
				->tournament($this->tournament->id)
				->asArray()
				->all();

			$deleted_teams = Team::deleteAll(["tournament_id" => $this->tournament->id]);
			$saved_teams = 0;
			$new_teams = 0;
			$old_teams = 0;

			$count = count($json);
			Yii::trace("$count lines of teams received", __METHOD__);

			for ($i = 0; $i < $count; $i++) {
				$item = $json[$i];

				$society = $this->handleSociety($item);

				$user = [];
				/*** A & B ***/

				foreach (["A" => 0, "B" => 1] as $key => $id) {
					/** @var User $user [$id] */

					if (!isset($item->speakers[$id])) continue;

					$speaker = $item->speakers[$id];
					$u_first = $speaker->firstname;
					$u_last = $speaker->lastname;
					$u_name = $u_first . " " . $u_last;
					$u_email = $speaker->eMail;
					$org_name = $item->organization->name;

					$user[$id] = User::find()
						->andWhere(["CONCAT(user.givenname,' ',user.surename)" => $u_name])
						->orWhere(["email" => $u_email])
						->all();

					if (count($user[$id]) > 1) {
						if (isset($this->FIX['t'][$u_name])) {
							$user[$id] = User::findOne($this->FIX['t'][$u_name]);
							if ($user[$id] instanceof User)
								unset($this->FIX['t'][$u_name]);
							else
								throw new Exception("User " . $key . " no resolved");
						} else {

							$matches = [];
							foreach ($user[$id] as $match) {
								$matches[$match->id] = $match->givenname . " " . $match->surename . " (" . $match->email . ")";
							}
							$this->FIX['t'][$u_name] = [
								"key"     => $u_name,
								"matches" => $matches,
								"msg"     => "Multiple user matches for " . $u_name . " " . $u_email . "(" . $org_name . ")"
							];
						}
					} else {
						if (count($user[$id]) == 0) {
							$user[$id] = User::NewViaImport($u_first, $u_last, $u_email, $society->id, true, $this->tournament);
							Yii::trace("New user created: $u_first $u_last, $u_email for $society->id", __METHOD__);
						} else {
							$user[$id] = $user[$id][0];
						}
					}
				}

				if ((isset($user[0]) && $user[0] instanceof User) || (isset($user[1]) && $user[1] instanceof User)) {
					$team = null;

					$uAID = (isset($user[0]) && $user[0] instanceof User) ? $user[0]->id : null;
					$uBID = (isset($user[1]) && $user[1] instanceof User) ? $user[1]->id : null;

					$old = $this->helperGetTeam($oldTeams, $uAID, $uBID);
					if (is_array($old)) {
						$team = new Team($old);
						$old_teams++;

					} else if ($society instanceof Society) {
						$team = new Team([
							"tournament_id" => $this->tournament->id,
							"name"          => $item->name,
							"speakerA_id"   => $uAID,
							"speakerB_id"   => $uBID,
							"society_id"    => $society->id,
						]);
						$new_teams++;
					}

					if ($team instanceof Team && !$team->save()) {
						throw new Exception("Can't save Team. " . print_r($team->attributes, true));
					} else
						$saved_teams++;
				}
			}

			Yii::$app->session->addFlash("notice", "Team Deleted: $deleted_teams. Saved: $saved_teams. Old: $old_teams. New: $new_teams.");
			Yii::trace("DebReg Team Import: Deleted: $deleted_teams. Saved: $saved_teams. Old: $old_teams. New: $new_teams.", __METHOD__);

			return true;

		} catch (Exception $ex) {
			Yii::$app->session->addFlash("error", $ex->getMessage() . " on " . __FUNCTION__ . ":" . $ex->getLine());
		}

		return false;
	}

	private function helperGetTeam($array, $Aid, $Bid)
	{
		$c = count($array);
		for ($i = 0; $i < $c; $i++) {
			if ($array[$i]["speakerA_id"] == $Aid && $array[$i]["speakerB_id"] == $Bid) return $array[$i];
		}

		return false;
	}

	/**
	 * @return bool
	 * @throws \yii\base\Exception
	 */
	public function getAccessKey()
	{
		if (strlen($this->key) > 0) return true;

		//grant_type=password&username=alice%40example.com&password=Password1!
		$data = [
			"grant_type" => "password",
			"username"   => $this->username,
			"password"   => $this->password,
		];
		$data = http_build_query($data);

		$context = stream_context_create([
			'http' => [
				'method'        => 'POST',
				'header'        => [
					'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
					'Content-Length: ' . strlen($data),
				],
				'content'       => $data,
				'ignore_errors' => true,
			]
		]);

		$json_string = @file_get_contents(self::DEBREG_URL . "/token", false, $context);

		if (strlen($json_string) == 0) throw new Exception("No content received at: " . self::DEBREG_URL . "/token", 404);
		$json = json_decode($json_string);

		if (isset($json->access_token) && isset($json->token_type)) {
			$this->key = $json->token_type . ' ' . $json->access_token;

			return true;
		}

		if (isset($json->error))
			return $json->error;
		else {
			echo "<h1>DebReg Error Message:</h1>";
			echo $json_string;
			exit();
		}

	}
}
