<?php

namespace common\models;

use algorithms\algorithms\StrictWUDCRules;
use Yii;
use frontend\components\VenueValidator;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * ContactForm is the model behind the contact form.
 */
class Outround extends Round
{
	public $outDebate = [];

	public $venues;
	public $adjudicators;

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return array_merge_recursive(parent::rules(), [
			[['venues', 'adjudicators'], 'required', 'on' => "2Step"],
			['outDebate', 'required', 'on' => "2Step", 'message' => Yii::t("app", 'Team position can\'t be blank')],
			['venues', VenueValidator::className()],
			[['outDebate', 'venues', 'adjudicators', 'level', 'type'], 'safe'],
		]);
	}

	public function scenarios()
	{
		$scenarios = parent::scenarios();
		$scenarios['1Step'] = ['level', 'type', 'motion', 'infoslide']; //Scenario Values Only Accepted
		return $scenarios;
	}


	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return array_merge(parent::attributeLabels(), [

		]);
	}


	public function generateOutround($runs = 1000)
	{
		try {
			set_time_limit(0); //Prevent timeout ... this can take time

			$DRAW = [];
			$this->lastrun_temp = 1;

			$venues = [];
			foreach ($this->venues as $v) {
				$venue = Venue::find()->tournament($this->tournament_id)->andWhere(["id" => $v])->one();
				if (!($venue instanceof Venue)) {
					$venue = new Venue([
						"name"          => $v,
						"group"         => Yii::t("app", "Outround"),
						"active"        => true,
						"tournament_id" => $this->tournament_id,
					]);
					$venue->save();
				}
				$venues[] = $venue;
			}

			$adjudicators = [];
			foreach ($this->adjudicators as $a) {
				$adju = Adjudicator::find()->tournament($this->tournament_id)->andWhere(["id" => $a])->one();
				if ($adju instanceof Adjudicator) {

					$adjudicator = $adju->attributes;
					$adjudicator["name"] = $adju->name;
					$adjudicator["societies"] = ArrayHelper::getColumn($adju->getSocieties(true)->asArray()->all(), "id");

					$strikedAdju = $adju->getStrikedAdjudicators()->asArray()->all();
					$adjudicator["strikedAdjudicators"] = $strikedAdju;

					$strikedTeam = $adju->getStrikedTeams()->asArray()->all();
					$adjudicator["strikedTeams"] = $strikedTeam;

					$adjudicator["pastAdjudicatorIDs"] = $adju->getPastAdjudicatorIDs($this->id);
					$adjudicator["pastTeamIDs"] = $adju->getPastTeamIDs($this->id);

					$adjudicators[] = $adjudicator;
				}
			}
			usort($adjudicators, ['common\models\Adjudicator', 'compare_strength']);

			foreach ($this->outDebate as $d) {
				$teams = [];
				foreach (Team::getPos() as $p) {
					$team = Team::find()->tournament($this->tournament_id)->andWhere(["id" => $d[$p . "_team"]])->one();
					if ($team instanceof Team)
						$teams[] = $team->attributes;
					else
						throw new Exception("Team not found " . $d["team_" . $p]);
				}

				$line = new DrawLine([
					"venue"        => array_pop($venues)->attributes,
					"teamsByArray" => $teams,
					"energyLevel"  => 0,
					"messages"     => [],
				]);

				$DRAW[] = $line;
			}

			$line = 0;
			while (count($adjudicators) > 0) {
				$adju = array_shift($adjudicators);
				/** @var DrawLine[] $DRAW */
				$DRAW[$line]->addAdjudicator($adju);

				if (isset($DRAW[$line + 1])) //Is there a next line
					$line = ($line + 1);
				else
					$line = 0;
			}

			/* Setup */
			/** @var StrictWUDCRules $algo */
			$algo = $this->tournament->getTabAlgorithmInstance();
			$algo->tournament_id = $this->tournament->id;
			$algo->energyConfig = EnergyConfig::loadArray($this->tournament->id);
			$algo->round_number = 1;

			$adjudicators_Query = Adjudicator::find()->active()->tournament($this->tournament->id);
			$adjudicators_strengthArray = ArrayHelper::getColumn(
				$adjudicators_Query->select("strength")->asArray()->all(),
				"strength"
			);

			$algo->average_adjudicator_strength = array_sum($adjudicators_strengthArray) / count($adjudicators_strengthArray);
			$algo->SD_of_adjudicators = self::stats_standard_deviation($adjudicators_strengthArray);

			Yii::beginProfile("Outround Draw by " . $runs);

			$algo->setDraw($DRAW);
			$new_draw = $algo->optimiseAdjudicatorAllocation($runs);
			$this->saveDraw($new_draw);

			$this->lastrun_temp = $algo->temp;
			$this->energy = $algo->best_energy;

			Yii::endProfile("Outround Draw by " . $runs);

			return true;

		} catch (\Exception $ex) {
			$this->addError("TabAlgorithm", $ex->getMessage() . " in " . $ex->getFile() . ":" . $ex->getLine());
		}

		return false;
	}
}
