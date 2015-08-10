<?php

namespace common\models;

use algorithms\algorithms\StrictWUDCRules;
use common\components\ObjectError;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * This is the model cla ss for table "round".
 *
 * @property integer         $id
 * @property integer         $number
 * @property integer         $tournament_id
 * @property integer         $type
 * @property integer         $energy
 * @property string          $motion
 * @property string          $infoslide
 * @property string          $time
 * @property bool            $published
 * @property bool            $displayed
 * @property bool            $closed
 * @property float           $lastrun_temp
 * @property integer         $lastrun_time
 * @property datetime        $prep_started
 * @property datetime        $finished_time
 * @property TabAfterRound[] $tabAfterRounds
 * @property Tournament      $tournament
 */
class Round extends \yii\db\ActiveRecord
{

	const STATUS_CREATED = 0;
	const STATUS_PUBLISHED = 1;
	const STATUS_DISPLAYED = 2;
	const STATUS_STARTED = 3;
	const STATUS_JUDGING = 4;
	const STATUS_CLOSED = 5;

	const TYP_IN = 0;
	const TYP_OUT = 1;
	const TYP_ESL = 2;
	const TYP_EFL = 3;
	const TYP_NOVICE = 4;

	public static function getTypeOptions($id = null)
	{
		$options = [
			self::TYP_IN     => Yii::t("app", "In-Round"),
			self::TYP_OUT    => Yii::t("app", "Out-Round"),
			self::TYP_EFL    => Yii::t("app", "ESL Out-Round"),
			self::TYP_EFL    => Yii::t("app", "EFL Out-Round"),
			self::TYP_NOVICE => Yii::t("app", "Novice Out-Round")
		];

		return (isset($options[$id])) ? $options[$id] : $options;
	}

	/**
	 * @inheritdoc
	 * @return TournamentQuery
	 */
	public static function find()
	{
		return new TournamentQuery(get_called_class());
	}

	static function statusLabel($code = null)
	{

		$labels = [
			0 => Yii::t("app", "Created"),
			1 => Yii::t("app", "Published"),
			2 => Yii::t("app", "Displayed"),
			3 => Yii::t("app", "Started"),
			4 => Yii::t("app", "Judging"),
			5 => Yii::t("app", "Finished"),
		];

		return (is_numeric($code)) ? $labels[$code] : $labels;
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'round';
	}

	public function getStatus()
	{

		if ($this->hasAllResultsEntered())
			return Round::STATUS_CLOSED;
		else if ($this->isJudgingTime())
			return Round::STATUS_JUDGING;
		else if ($this->isStartingTime())
			return Round::STATUS_STARTED;
		else if ($this->displayed == 1)
			return Round::STATUS_DISPLAYED;
		else if ($this->published == 1)
			return Round::STATUS_PUBLISHED;
		else if ($this->time)
			return Round::STATUS_CREATED;
		else
			throw new Exception("Unknow Round status for Round" . $this->number . " No create time");
	}

	public function hasAllResultsEntered()
	{
		return false;
	}

	public function isJudgingTime()
	{
		$debatetime = (8 * 7) + 8;
		$preptime = 15;
		if ($this->prep_started) {
			$judgeTime = strtotime($this->prep_started) + $preptime + $debatetime;

			if (time() > $judgeTime)
				return true;
		}

		return false;
	}

	public function isStartingTime()
	{
		$preptime = 15;
		if ($this->prep_started) {
			$prepende = strtotime($this->prep_started) + $preptime;

			if (time() > $prepende)
				return true;
		}

		return false;
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['number', 'tournament_id', 'motion'], 'required'],
			[['id', 'number', 'tournament_id', 'published'], 'integer'],
			[['motion', 'infoslide'], 'string'],
			[['time'], 'safe']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'            => Yii::t('app', 'Round ID'),
			'id'            => Yii::t('app', 'Round Number'),
			'tournament_id' => Yii::t('app', 'Tournament ID'),
			'energy'        => Yii::t('app', 'Energy'),
			'motion'        => Yii::t('app', 'Motion'),
			'infoslide'     => Yii::t('app', 'Info Slide'),
			'time'          => Yii::t('app', 'Time'),
			'published'     => Yii::t('app', 'Published'),
			'displayed'     => Yii::t('app', 'Displayed'),
			'prep_started'  => Yii::t('app', 'PrepTime started'),
			'lastrun_temp'  => Yii::t('app', 'Last Temperature'),
			'lastrun_time'  => Yii::t('app', 'ms to calculate'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getDrawAfterRounds()
	{
		return $this->hasMany(TabAfterRound::className(), ['round_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTournament()
	{
		return $this->hasOne(Tournament::className(), ['id' => 'tournament_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getDebates()
	{
		return $this->hasMany(Debate::className(), ['round_id' => 'id', 'tournament_id' => 'tournament_id']);
	}

	/**
	 * Generate a draw for the model
	 */
	public function generateWorkingDraw()
	{
		try {
			set_time_limit(0); //Prevent timeout ... this can take time

			$venues = Venue::find()->active()->tournament($this->tournament->id)->asArray()->all();
			$teams = Team::find()->active()->tournament($this->tournament->id)->asArray()->all();

			$adjudicators_Query = Adjudicator::find()->active()->tournament($this->tournament->id);

			$adjudicatorsObjects = $adjudicators_Query->all();

			$panel = [];
			$panelsObjects = Panel::find()->where([
				'is_preset'     => 1,
				'used'          => 0,
				'tournament_id' => $this->tournament_id])->all();

			$active_rooms = (count($teams) / 4);

			$AdjIDsalreadyinPanels = [];

			foreach ($panelsObjects as $p) {
				$panelAdju = [];
				$total = 0;

				/** @var Panel $p */
				foreach ($p->getAdjudicatorsObjects() as $adju) {
					/** @var Adjudicator $adju */
					$AdjIDsalreadyinPanels[] = $adju->id;

					$adjudicator = $adju->attributes;
					$adjudicator["name"] = $adju->name;
					$adjudicator["societies"] = ArrayHelper::getColumn($adju->getSocieties(true)->asArray()->all(), "id");

					$strikedAdju = $adju->getStrikedAdjudicators()->asArray()->all();
					$adjudicator["strikedAdjudicators"] = $strikedAdju;

					$strikedTeam = $adju->getStrikedTeams()->asArray()->all();
					$adjudicator["strikedTeams"] = $strikedTeam;

					$adjudicator["pastAdjudicatorIDs"] = $adju->getPastAdjudicatorIDs($this->id);
					$adjudicator["pastTeamIDs"] = $adju->getPastTeamIDs(true);

					$total += $adju->strength;

					$panelAdju[] = $adjudicator;
				}

				$panel[] = [
					"id"       => $p->id,
					"strength" => intval($total / count($panelAdju)),
					"adju"     => $panelAdju,
				];
			}

			$adjudicators = [];
			for ($i = 0; $i < count($adjudicatorsObjects); $i++) {

				if (!in_array($adjudicatorsObjects[$i]->id, $AdjIDsalreadyinPanels)) {
					//Only add if not already in Preset Panel
					/** @var $adjudicatorsObjects [$i] Adjudicator */
					$adjudicators[$i] = $adjudicatorsObjects[$i]->attributes;
					$adjudicators[$i]["name"] = $adjudicatorsObjects[$i]->name;
					$adjudicators[$i]["societies"] = ArrayHelper::getColumn($adjudicatorsObjects[$i]->getSocieties(true)->asArray()->all(), "id");

					$strikedAdju = $adjudicatorsObjects[$i]->getStrikedAdjudicators()->asArray()->all();
					$adjudicators[$i]["strikedAdjudicators"] = $strikedAdju;

					$strikedTeam = $adjudicatorsObjects[$i]->getStrikedTeams()->asArray()->all();
					$adjudicators[$i]["strikedTeams"] = $strikedTeam;

					$adjudicators[$i]["pastAdjudicatorIDs"] = $adjudicatorsObjects[$i]->getPastAdjudicatorIDs();
					$adjudicators[$i]["pastTeamIDs"] = $adjudicatorsObjects[$i]->getPastTeamIDs();
				}
			}

			$adjudicators_strengthArray = ArrayHelper::getColumn(
				$adjudicators_Query->select("strength")->asArray()->all(),
				"strength"
			);

			/* Check variables */
			if (count($teams) < 4)
				throw new Exception(Yii::t("app", "Not enough Teams to fill a single room - (active: {teams_count})", ["teams_count" => count($teams)]), "500");
			if (count($adjudicatorsObjects) < 2)
				throw new Exception(Yii::t("app", "At least two Adjudicators are necessary - (active: {count_adju})", ["count_adju" => count($adjudicatorsObjects)]), "500");
			if (count($teams) % 4 != 0)
				throw new Exception(Yii::t("app", "Amount of active Teams must be divided by 4 ;) - (active: {count_teams})", ["count_teams" => count($teams)]), "500");
			if ($active_rooms > count($venues))
				throw new Exception(Yii::t("app", "Not enough active Rooms (active: {active_rooms} required: {required})", [
					"active_rooms" => count($venues),
					"required"     => $active_rooms,
				]), "500");
			if ($active_rooms > count($adjudicatorsObjects))
				throw new Exception(Yii::t("app", "Not enough adjudicators (active: {active}  min-required: {required})", [
					"active"   => count($adjudicatorsObjects),
					"required" => $active_rooms,
				]), "500");
			if ($active_rooms > (count($adjudicators) + count($panel)))
				throw new Exception(Yii::t("app",
					"Not enough free adjudicators with this preset panel configuration. (fillable rooms: {active}  min-required: {required})", [
						"active"   => (count($adjudicatorsObjects) + count($panelsObjects)),
						"required" => $active_rooms,
					]), "500");

			/* Setup */
			/** @var StrictWUDCRules $algo */
			$algo = $this->tournament->getTabAlgorithmInstance();
			$algo->tournament_id = $this->tournament->id;
			$algo->energyConfig = EnergyConfig::loadArray($this->tournament->id);
			$algo->round_number = $this->number;

			if (count($adjudicators_strengthArray) == 0) {
				$algo->average_adjudicator_strength = 0;
				$algo->SD_of_adjudicators = 0;
			} else {
				$algo->average_adjudicator_strength = array_sum($adjudicators_strengthArray) / count($adjudicators_strengthArray);
				$algo->SD_of_adjudicators = self::stats_standard_deviation($adjudicators_strengthArray);
			}

			Yii::trace("Ready to set the draw", __METHOD__);

			$draw = $algo->makeDraw($venues, $teams, $adjudicators, $panel);

			Yii::trace("makeDraw returns " . count($draw) . "lines", __METHOD__);

			$this->saveDraw($draw);

			Yii::trace("We saved the draw.", __METHOD__);

			$this->lastrun_temp = $algo->temp;
			$this->energy = $algo->best_energy;

			return true;
		} catch (Exception $ex) {
			//throw $ex;
			$this->addError("TabAlgorithm", $ex->getMessage());
		}

		return false;
	}

	public static function stats_standard_deviation(array $a)
	{
		$n = count($a);
		if ($n === 0) {
			trigger_error("The array has zero elements", E_USER_WARNING);

			return false;
		}
		if ($n === 1) {
			trigger_error("The array has only 1 element", E_USER_WARNING);

			return false;
		}
		$mean = array_sum($a) / $n;
		$carry = 0.0;
		foreach ($a as $val) {
			$d = ((double)$val) - $mean;
			$carry += $d * $d;
		};

		return sqrt($carry / $n);
	}

	/**
	 * Saves a full draw
	 *
	 * @param DrawLine[] $draw
	 *
	 * @throws \yii\base\Exception
	 */
	private function saveDraw($draw)
	{
		Yii::trace("Save Draw with " . count($draw) . "lines", __METHOD__);
		$set_pp = 0;
		$lineAdju_total = 0;
		foreach ($draw as $line) {
			/* @var $line DrawLine */

			if (!$line->hasPresetPanel) {
				$panel = new Panel();
				$panel->tournament_id = $this->tournament_id;
				$panel->strength = $line->strength;

				//Save Panel
				if (!$panel->save())
					throw new Exception(Yii::t("app", "Can't save Panel {message}", ["message" => ObjectError::getMsg($panel)]));

				$line->panelID = $panel->id;

				$chairSet = false;
				foreach ($line->adjudicators as $judge) {
					try {
						/* @var $judge Adjudicator */
						$alloc = new AdjudicatorInPanel();
						$alloc->adjudicator_id = $judge["id"];
						$alloc->panel_id = $line->panelID;
						if (!$chairSet) {
							$alloc->function = Panel::FUNCTION_CHAIR;
							$chairSet = true; //only on first run
						} else
							$alloc->function = Panel::FUNCTION_WING;

						if (!$alloc->save())
							throw new Exception($judge["name"] . " could not be saved: " . ObjectError::getMsg($alloc));

					} catch (Exception $ex) {
						Yii::error($judge["id"] . "-" . $judge["name"] . ": " . $ex->getMessage(), __METHOD__);
						Yii::$app->session->addFlash("error", $judge["id"] . "-" . $judge["name"] . ": " . $ex->getMessage());
					}
				}
			} else {
				//is a preset Panel
				$presetP = Panel::find()->tournament($this->tournament_id)->andWhere(["id" => $line->panelID])->one();
				$alreadyIn = ArrayHelper::getColumn($presetP->getAdjudicatorsObjects(), "id");
				$presetP->used = 1;

				if (!$presetP->save())
					Yii::error("Cant save preset panel" . ObjectError::getMsg($presetP), __METHOD__);
				else
					$set_pp++;

				foreach ($line->adjudicators as $judge) {
					try {
						if (!in_array($judge["id"], $alreadyIn)) {
							/* @var $judge Adjudicator */
							$alloc = new AdjudicatorInPanel();
							$alloc->adjudicator_id = $judge["id"];
							$alloc->panel_id = $line->panelID;
							$alloc->function = Panel::FUNCTION_WING;

							if (!$alloc->save())
								throw new Exception($judge["name"] . " could not be saved: " . ObjectError::getMsg($alloc));
						}

					} catch (Exception $ex) {
						Yii::error($judge["id"] . "-" . $judge["name"] . ": " . $ex->getMessage(), __METHOD__);
						Yii::$app->session->addFlash("error", $judge["id"] . "-" . $judge["name"] . ": " . $ex->getMessage());
					}
				}
			}

			$debate = new Debate();
			$debate->round_id = $this->id;
			$debate->tournament_id = $this->tournament_id;
			$debate->og_team_id = $line->OG["id"];
			$debate->oo_team_id = $line->OO["id"];
			$debate->cg_team_id = $line->CG["id"];
			$debate->co_team_id = $line->CO["id"];
			$debate->venue_id = $line->venue["id"];
			$debate->panel_id = $line->panelID;
			$debate->energy = $line->energyLevel;
			$debate->setMessages($line->messages);

			if (!$debate->save())
				throw new Exception(Yii::t("app", "Can't save Debate {message}", ["message" => print_r($debate->getErrors(), true)]));
			else {
				$lineAdju = $debate->panel->getAdjudicators()->count();
				$lineAdju_total += $lineAdju;
				Yii::trace("Debate #" . $debate->id . " saved with " . $lineAdju . " Adjudicators", __METHOD__);
			}
		}
		Yii::trace($set_pp . " PP saved as used", __METHOD__);
		Yii::trace($lineAdju_total . " Adjudicators saved", __METHOD__);
	}

	public function improveAdjudicator($runs)
	{
		set_time_limit(0); //Prevent timeout ... this can take time

		/** @var DrawLine[] $DRAW */
		$DRAW = [];

		if (is_int(intval($runs)) && $runs <= 100000) {
			$runs = intval($runs);
		} else
			$runs = null;

		/* Reconstruct DrawArray */
		Yii::beginProfile("Reconstruct DrawArray");
		$models = $this->debates;
		foreach ($models as $model) {

			$line = $this->reconstructDebate($model);
			$line = $this->reconstructPanel($model->panel->adjudicatorInPanels, $line);

			$DRAW[] = $line;
		}

		/** Delete Debates */
		foreach ($models as $debate) {
			/** @var Debate $debate * */
			foreach ($debate->panel->adjudicatorInPanels as $aj)
				$aj->delete();

			$panelid = $debate->panel_id;
			$debate->delete();
			Panel::deleteAll(["id" => $panelid]);
		}
		Yii::endProfile("Reconstruct DrawArray");

		/* Setup */
		$algo = $this->tournament->getTabAlgorithmInstance();
		$algo->tournament_id = $this->tournament->id;
		$algo->energyConfig = EnergyConfig::loadArray($this->tournament->id);
		$algo->round_number = $this->number;

		$adjudicators_Query = Adjudicator::find()->active()->tournament($this->tournament->id);
		$adjudicators_strengthArray = ArrayHelper::getColumn(
			$adjudicators_Query->select("strength")->asArray()->all(),
			"strength"
		);

		$algo->average_adjudicator_strength = array_sum($adjudicators_strengthArray) / count($adjudicators_strengthArray);
		$algo->SD_of_adjudicators = self::stats_standard_deviation($adjudicators_strengthArray);

		Yii::beginProfile("Improve Draw by " . $runs);

		$algo->setDraw($DRAW);
		$new_draw = $algo->optimiseAdjudicatorAllocation($runs, $this->lastrun_temp);
		$this->saveDraw($new_draw);

		$this->lastrun_temp = $algo->temp;
		$this->energy = $algo->best_energy;

		Yii::endProfile("Improve Draw by " . $runs);

		return true;
	}

	private function reconstructDebate($model)
	{
		/** @var Debate $model */
		$line = new DrawLine([
			"id"           => $model->id,
			"venue"        => $model->venue->attributes,
			"teamsByArray" => [
				Team::OG => $model->og_team->attributes,
				Team::OO => $model->oo_team->attributes,
				Team::CG => $model->cg_team->attributes,
				Team::CO => $model->co_team->attributes,
			],
			"panelID"      => $model->panel_id,
			"energyLevel"  => $model->energy,
			"messages"     => $model->getMessages(),
		]);

		return $line;
	}

	private function reconstructPanel($adjudicatorInPanels, $drawline)
	{
		/** @var Panel $panel */
		foreach ($adjudicatorInPanels as $inPanel) {

			$adju = $inPanel->adjudicator;
			$adjudicator = $adju->attributes;
			$adjudicator["name"] = $adju->name;
			$adjudicator["societies"] = ArrayHelper::getColumn($adju->getSocieties(true)->asArray()->all(), "id");

			$strikedAdju = $adju->getStrikedAdjudicators()->asArray()->all();
			$adjudicator["strikedAdjudicators"] = $strikedAdju;

			$strikedTeam = $adju->getStrikedTeams()->asArray()->all();
			$adjudicator["strikedTeams"] = $strikedTeam;

			$adjudicator["pastAdjudicatorIDs"] = $adju->getPastAdjudicatorIDs($this->id);
			$adjudicator["pastTeamIDs"] = $adju->getPastTeamIDs(true);

			if ($inPanel->function == Panel::FUNCTION_CHAIR)
				$drawline->addChair($adjudicator);
			else
				$drawline->addAdjudicator($adjudicator);
		}

		return $drawline;
	}

	/**
	 * Update the Energy of certain lines and updates the database with the new energy and messages.
	 *
	 * @param array $updateLines
	 *
	 * @return array
	 * @throws \yii\base\Exception
	 */
	public function updateEnergy($updateLines = [])
	{
		/** @var DrawLine[] $DRAW */
		$miniDraw = [];

		/* Reconstruct DrawArray */
		foreach ($updateLines as $key => $updateline) {

			$model = Debate::findOne($updateline);
			if ($model instanceof Debate) {
				/** @var Debate $model */
				$drawline = $this->reconstructDebate($model);
				$drawline = $this->reconstructPanel($model->panel->adjudicatorInPanels, $drawline);

				$miniDraw[$key] = $drawline;
			}
		}

		/* Setup */
		/** @var StrictWUDCRules $algo */
		$algo = $this->tournament->getTabAlgorithmInstance();
		$algo->tournament_id = $this->tournament->id;
		$algo->energyConfig = EnergyConfig::loadArray($this->tournament->id);
		$algo->round_number = $this->number;

		$adjudicators_Query = Adjudicator::find()->active()->tournament($this->tournament->id);
		$adjudicators_strengthArray = ArrayHelper::getColumn(
			$adjudicators_Query->select("strength")->asArray()->all(),
			"strength"
		);

		$algo->average_adjudicator_strength = array_sum($adjudicators_strengthArray) / count($adjudicators_strengthArray);
		$algo->SD_of_adjudicators = self::stats_standard_deviation($adjudicators_strengthArray);

		$returnLine = [];
		foreach ($miniDraw as $key => $miniline) {
			$newLine = $algo->calcEnergyLevel($miniline);
			$debate = Debate::findOne($newLine->id);
			if ($debate instanceof Debate) {
				$debate->energy = $newLine->energyLevel;
				$debate->setMessages($newLine->messages);
				if (!$debate->save()) {
					throw new Exception(
						Yii::t("app", "Can't save debate: <br> {attr}", [
							"attr" => ObjectError::getMsg($debate),
						])
					);
				}
			} else {
				Yii::$app->session->addFlash("error", Yii::t("app", "No Debate #{num} found to update", ["num" => $newLine->id]));
			}

			$returnLine[$key] = $newLine;
		}

		return $returnLine;
	}

	public function getAmountSwingTeams()
	{
		return Team::find()->active()->tournament($this->tournament_id)->andWhere(["isSwing" => 1])->count();
	}

}
