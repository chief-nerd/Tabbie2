<?php

namespace common\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "round".


*
*@property integer         $id
 * @property integer         $number
 * @property integer         $tournament_id
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
class Round extends \yii\db\ActiveRecord {

	const STATUS_CREATED   = 0;
	const STATUS_PUBLISHED = 1;
	const STATUS_DISPLAYED = 2;
	const STATUS_STARTED   = 3;
	const STATUS_JUDGING   = 4;
	const STATUS_CLOSED    = 5;

	/**
	 * @inheritdoc
	 * @return TournamentQuery
	 */
	public static function find() {
		return new TournamentQuery(get_called_class());
	}

	static function statusLabel($code = null) {

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

	public function getStatus() {

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

	public function isJudgingTime() {
		$debatetime = (8 * 7) + 8;
		$preptime = 15;
		if ($this->prep_started) {
			$judgeTime = strtotime($this->prep_started) + $preptime + $debatetime;

			if (time() > $judgeTime)
				return true;
		}
		return false;
	}

	public function isStartingTime() {
		$preptime = 15;
		if ($this->prep_started) {
			$prepende = strtotime($this->prep_started) + $preptime;

			if (time() > $prepende)
				return true;
		}
		return false;
	}

	public function hasAllResultsEntered() {
		return false;
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'round';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
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
	public function attributeLabels() {
		return [
			'id' => Yii::t('app', 'Round ID'),
			'id' => Yii::t('app', 'Round Number'),
			'tournament_id' => Yii::t('app', 'Tournament ID'),
			'energy' => Yii::t('app', 'Energy'),
			'motion' => Yii::t('app', 'Motion'),
			'infoslide' => Yii::t('app', 'Info Slide'),
			'time' => Yii::t('app', 'Time'),
			'published' => Yii::t('app', 'Published'),
			'displayed' => Yii::t('app', 'Displayed'),
			'prep_started' => Yii::t('app', 'PrepTime started'),
			'lastrun_temp' => Yii::t('app', 'Last Temperature'),
			'lastrun_time' => Yii::t('app', 'ms to calculate'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getDrawAfterRounds() {
		return $this->hasMany(TabAfterRound::className(), ['round_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTournament() {
		return $this->hasOne(Tournament::className(), ['id' => 'tournament_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getDebates() {
		return $this->hasMany(Debate::className(), ['round_id' => 'id', 'tournament_id' => 'tournament_id']);
	}

	/**
	 * Generate a draw for the model
	 */
	public function generateWorkingDraw() {
		try {
			set_time_limit(0); //Prevent timeout ... this can take time

			$venues = Venue::find()->active()->tournament($this->tournament->id)->asArray()->all();
			$teams = Team::find()->active()->tournament($this->tournament->id)->asArray()->all();

			$adjudicators_Query = Adjudicator::find()->active()->tournament($this->tournament->id);

			$adjudicatorsObjects = $adjudicators_Query->all();
			$adjudicators = [];
			for ($i = 0; $i < count($adjudicatorsObjects); $i++) {
				$adjudicators[$i] = $adjudicatorsObjects[$i]->attributes;
				$adjudicators[$i]["name"] = $adjudicatorsObjects[$i]->name;

				$strikedAdju = $adjudicatorsObjects[$i]->getStrikedAdjudicators()->asArray()->all();
				$adjudicators[$i]["strikedAdjudicators"] = $strikedAdju;

				$strikedTeam = $adjudicatorsObjects[$i]->getStrikedTeams()->asArray()->all();
				$adjudicators[$i]["strikedTeams"] = $strikedTeam;

				$adjudicators[$i]["pastAdjudicatorIDs"] = $adjudicatorsObjects[$i]->getPastAdjudicatorIDs();
				$adjudicators[$i]["pastTeamIDs"] = $adjudicatorsObjects[$i]->getPastTeamIDs();
			}


			$adjudicators_strengthArray = ArrayHelper::getColumn(
				$adjudicators_Query->select("strength")->asArray()->all(),
				"strength"
			);

			/* Setup */
			$algo = $this->tournament->getTabAlgorithmInstance();
			$algo->tournament_id = $this->tournament->id;
			$algo->energyConfig = EnergyConfig::loadArray($this->tournament->id);
			$algo->round_number = $this->number;
			$algo->average_adjudicator_strength = array_sum($adjudicators_strengthArray) / count($adjudicators_strengthArray);
			$algo->SD_of_adjudicators = $this->stats_standard_deviation($adjudicators_strengthArray);

			$draw = $algo->makeDraw($venues, $teams, $adjudicators);
			$this->saveDraw($draw);

			$this->lastrun_temp = $algo->temp;
			$this->energy = $algo->best_energy;

			return true;
		} catch (Exception $ex) {
			$this->addError("TabAlgorithmus", $ex->getMessage());
		}
		return false;
	}

	private function saveDraw($draw) {
		foreach ($draw as $line) {
			/* @var $line DrawLine */

			if (!$line->hasPresetPanel) {
				$panel = new Panel();
				$panel->tournament_id = $this->tournament_id;
				$panel->strength = $line->strength;
				//Save Panel
				if (!$panel->save())
					throw new Exception("Can't save Panel " . print_r($panel->getErrors(), true));

				$line->panelID = $panel->id;

				$chairSet = false;
				foreach ($line->adjudicators as $judge) {
					/* @var $judge Adjudicator */
					$alloc = new AdjudicatorInPanel();
					$alloc->adjudicator_id = $judge["id"];
					$alloc->panel_id = $line->panelID;
					if (!$chairSet) {
						$alloc->function = Panel::FUNCTION_CHAIR;
						$chairSet = true; //only on first run
					}
					else
						$alloc->function = Panel::FUNCTION_WING;

					if (!$alloc->save())
						throw new Exception("Can't save AdjudicatorInPanel " . print_r($alloc->getErrors(), true));
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
				throw new Exception("Can't save Debate " . print_r($debate->getErrors(), true));
		}
	}

	private function stats_standard_deviation(array $a) {
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

	public function improveAdjudicator($runs) {
		//set_time_limit(0); //Prevent timeout ... this can take time

		/** @var DrawLine[] $DRAW */
		$DRAW = [];

		if (is_int(intval($runs)) && $runs <= 100000) {
			$runs = intval($runs);
		}
		else
			$runs = null;

		/* Reconstruct DrawArray */
		Yii::beginProfile("Reconstruct DrawArray");
		$models = $this->debates;
		foreach ($models as $model) {
			/** @var Debate $model */
			$line = new DrawLine([
				"id" => $model->id,
				"venue" => $model->venue,
				"teamsByArray" => [
					Team::OG => $model->og_team,
					Team::OO => $model->oo_team,
					Team::CG => $model->cg_team,
					Team::CO => $model->co_team,
				],
				"panelID" => $model->panel_id,
				"energyLevel" => $model->energy,
				"messages" => $model->getMessages(),
			]);

			/** @var Panel $panel */
			foreach ($model->panel->adjudicatorInPanels as $inPanel) {

				if ($inPanel->function == Panel::FUNCTION_CHAIR)
					$line->addChair($inPanel->adjudicator);
				else
					$line->addAdjudicator($inPanel->adjudicator);
			}
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
		$algo->SD_of_adjudicators = $this->stats_standard_deviation($adjudicators_strengthArray);

		Yii::beginProfile("Improve Draw by " . $runs);

		$algo->setDraw($DRAW);
		$new_draw = $algo->optimiseAdjudicatorAllocation($runs, $this->lastrun_temp);
		$this->saveDraw($new_draw);

		$this->lastrun_temp = $algo->temp;
		$this->energy = $algo->best_energy;

		Yii::endProfile("Improve Draw by " . $runs);

		return true;
	}

	public function getAmountSwingTeams() {
		return Team::find()->active()->tournament($this->tournament_id)->andWhere(["isSwing" => 1])->count();
	}

}
