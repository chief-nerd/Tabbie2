<?php
namespace tests\codeception\common\algorithm;

use algorithms\algorithms\StrictWUDCRules;
use common\models\DrawLine;
use common\models\EnergyConfig;
use common\models\Panel;
use common\models\Round;
use common\models\Tournament;
use Yii;
use Codeception\Specify;
use Codeception\Verify;
use common\models\Team;
use Codeception\Util\Debug;
use common\models\Venue;
use common\models\Adjudicator;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * Test StrictWUDCRules Class
 */
class StrictWUDCRulesTest extends DbTestCase
{

	/**
	 * @var StrictWUDCRules
	 */
	public $algo = false;
	/** @var  Tournament */
	public $tournament;

	public $teams;
	public $adjudicators;
	public $venues;
	public $DRAW;

	public function setUp()
	{
		parent::setUp();

		try {
			$this->tournament = Tournament::findByPk(1);
			if (!$this->tournament instanceof Tournament) throw new Exception("Setup fail");

			$this->venues = Venue::find()->active()->tournament($this->tournament->id)->asArray()->all();
			$this->teams = Team::find()->active()->tournament($this->tournament->id)->asArray()->all();

			$adjudicators_Query = Adjudicator::find()->active()->tournament($this->tournament->id);

			$adjudicatorsObjects = $adjudicators_Query->all();

			$panel = [];
			$panelsObjects = Panel::find()->where([
				'is_preset'     => 1,
				'used'          => 0,
				'tournament_id' => $this->tournament->id])->all();

			$active_rooms = (count($this->teams) / 4);

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
					$adjudicator["societies"] = [];

					$strikedAdju = $adju->getStrikedAdjudicators()->asArray()->all();
					$adjudicator["strikedAdjudicators"] = $strikedAdju;

					$strikedTeam = $adju->getStrikedTeams()->asArray()->all();
					$adjudicator["strikedTeams"] = $strikedTeam;

					$adjudicator["pastAdjudicatorIDs"] = $adju->getPastAdjudicatorIDs();
					$adjudicator["pastTeamIDs"] = $adju->getPastTeamIDs();

					$total += $adju->strength;

					$panelAdju[] = $adjudicator;
				}

				$panel[] = [
					"id"       => $p->id,
					"strength" => intval($total / count($panelAdju)),
					"adju"     => $panelAdju,
				];
			}

			$this->adjudicators = [];
			for ($i = 0; $i < count($adjudicatorsObjects); $i++) {

				if (!in_array($adjudicatorsObjects[$i]->id, $AdjIDsalreadyinPanels)) {
					//Only add if not already in Preset Panel

					$this->adjudicators[$i] = $adjudicatorsObjects[$i]->attributes;
					$this->adjudicators[$i]["name"] = "Name " . $adjudicatorsObjects[$i]["id"]; //$adjudicatorsObjects[$i]->name;
					$this->adjudicators[$i]["societies"] = [];

					$strikedAdju = $adjudicatorsObjects[$i]->getStrikedAdjudicators()->asArray()->all();
					$this->adjudicators[$i]["strikedAdjudicators"] = $strikedAdju;

					$strikedTeam = $adjudicatorsObjects[$i]->getStrikedTeams()->asArray()->all();
					$this->adjudicators[$i]["strikedTeams"] = $strikedTeam;

					$this->adjudicators[$i]["pastAdjudicatorIDs"] = $adjudicatorsObjects[$i]->getPastAdjudicatorIDs();
					$this->adjudicators[$i]["pastTeamIDs"] = $adjudicatorsObjects[$i]->getPastTeamIDs();
				}
			}

			$adjudicators_strengthArray = ArrayHelper::getColumn(
				$adjudicators_Query->select("strength")->asArray()->all(),
				"strength"
			);

			/* Check variables */
			if (count($this->teams) < 4)
				throw new Exception(Yii::t("app", "Not enough Teams to fill a single room - (active: {teams_count})", ["teams_count" => count($this->teams)]), "500");
			if (count($adjudicatorsObjects) < 2)
				throw new Exception(Yii::t("app", "At least two Adjudicators are necessary - (active: {count_adju})", ["count_adju" => count($adjudicatorsObjects)]), "500");
			if (count($this->teams) % 4 != 0)
				throw new Exception(Yii::t("app", "Amount of active Teams must be divided by 4 ;) - (active: {count_teams})", ["count_teams" => count($this->teams)]), "500");
			if ($active_rooms > count($this->venues))
				throw new Exception(Yii::t("app", "Not enough active Rooms (active: {active_rooms} required: {required})", [
					"active_rooms" => count($this->venues),
					"required"     => $active_rooms,
				]), "500");
			if ($active_rooms > count($adjudicatorsObjects))
				throw new Exception(Yii::t("app", "Not enough adjudicators (active: {active}  min-required: {required})", [
					"active"   => count($adjudicatorsObjects),
					"required" => $active_rooms,
				]), "500");
			if ($active_rooms > (count($this->adjudicators) + count($panel)))
				throw new Exception(Yii::t("app",
					"Not enough free adjudicators with this preset panel configuration. (fillable rooms: {active}  min-required: {required})", [
						"active"   => (count($adjudicatorsObjects) + count($panelsObjects)),
						"required" => $active_rooms,
					]), "500");

			$this->algo = $this->tournament->getTabAlgorithmInstance();
			$this->algo->tournament_id = $this->tournament->id;
			$this->algo->energyConfig = EnergyConfig::loadArray($this->tournament->id);
			$this->algo->round_number = 1;

			if (count($adjudicators_strengthArray) == 0) {
				$this->algo->average_adjudicator_strength = 0;
				$this->algo->SD_of_adjudicators = 0;
			} else {
				$this->algo->average_adjudicator_strength = array_sum($adjudicators_strengthArray) / count($adjudicators_strengthArray);
				$this->algo->SD_of_adjudicators = Round::stats_standard_deviation($adjudicators_strengthArray);
			}


		} catch (\Exception $ex) {
			throw new Exception($ex->getMessage() . " in " . $ex->getFile() . ":" . $ex->getLine());
		}
	}

	public function testSortAdjudicators()
	{
		if ($this->algo === false) {
			throw new Exception("Setup fail");
		}
		if (count($this->adjudicators) <= 2)
			throw new Exception("no data");

		$new_adj = $this->algo->sort_adjudicators($this->adjudicators);

		expect("Preserve Amount", count($new_adj))->equals(count($this->adjudicators));
		for ($i = 0; $i < count($new_adj); $i++) {
			/* @var $t Team */
			$a = $new_adj[$i];

			$this->assertTrue(is_array($a));
			if ($i > 0) {
				expect("Sort Order", $a["strength"])->lessOrEquals($new_adj[$i - 1]["strength"]);
			}
		}
	}

	public function testSortAndRandomisationOfTeams()
	{
		$new_teams = $this->algo->sort_teams($this->teams);
		$new_teams = $this->algo->randomise_within_points($new_teams);

		expect("Preserve Amount", count($new_teams))->equals(count($this->teams));
		for ($i = 0; $i < count($new_teams); $i++) {
			$this->assertTrue(is_array($new_teams[$i]));
			if ($i > 0) {
				expect("Sort Order", $new_teams[$i]["points"])->lessOrEquals($new_teams[$i - 1]["points"]);
			}
		}
	}

	public function testSwapTeams()
	{

		$pos_a = rand(0, 3);
		$id_a = rand(0, count($this->teams) - 1);
		$line_a = new \common\models\DrawLine();
		$line_a->setTeamOn($pos_a, $this->teams[$id_a]);

		$pos_b = rand(0, 3);
		$id_b = rand(0, count($this->teams) - 1);
		$line_b = new \common\models\DrawLine();
		$line_b->setTeamOn($pos_b, $this->teams[$id_b]);

		$this->algo->swap_teams($line_a, $pos_a, $line_b, $pos_b);

		expect($line_a->getTeamOn($pos_a)["id"])->equals($this->teams[$id_b]["id"]);
		expect($line_b->getTeamOn($pos_b)["id"])->equals($this->teams[$id_a]["id"]);
	}

	public function testSwapAdjudicator()
	{
		Debug::debug("\n");
		try {
			$pos_a = rand(0, 3);
			$pos_b = rand(0, 3);
			$offset = 0;

			foreach ([0, 1] as $prefix) {
				$line[$prefix] = new DrawLine(["id" => $prefix]);

				for ($i = 0; $i <= 3; $i++) {
					$line[$prefix]->setAdjudicator($i, $this->adjudicators[$i + $offset]);
				}
				$offset = $i;

				for ($i = 0; $i <= 3; $i++) {
					$line[$prefix]->setTeamOn($i, $this->teams[rand(1, count($this->teams) - 1)]);
				}

				$line[$prefix] = $this->algo->calcEnergyLevel($line[$prefix]);
			}

			$line_a = clone $line[0];
			$line_b = clone $line[1];

			Debug::debug("PosA:" . $pos_a . "\tPosB: " . $pos_b);
			Debug::debug("");
			Debug::debug("A:" . implode(", ", ArrayHelper::getColumn($line_a->getAdjudicators(), "id")));
			Debug::debug("B:" . implode(", ", ArrayHelper::getColumn($line_b->getAdjudicators(), "id")));
			Debug::debug("");

			$return_lines = $this->algo->swap_adjudicator($line_a, $pos_a, $line_b, $pos_b);

			$line_a = $return_lines[0];
			$line_b = $return_lines[1];

			Debug::debug("A:" . implode(", ", ArrayHelper::getColumn($line_a->getAdjudicators(), "id")));
			Debug::debug("B:" . implode(", ", ArrayHelper::getColumn($line_b->getAdjudicators(), "id")));

			expect("A change:", $line_a->getAdjudicator($pos_a)["id"])->equals($line[1]->getAdjudicator($pos_b)["id"]);
			expect("B change:", $line_b->getAdjudicator($pos_b)["id"])->equals($line[0]->getAdjudicator($pos_a)["id"]);

		} catch (\Exception $ex) {
			throw new Exception($ex->getMessage() . " in " . $ex->getFile() . ":" . $ex->getLine());
		}
	}

	public function testSwapAdjudicatorSameLine()
	{
		Debug::debug("\n");
		try {
			$pos_a = rand(0, 3);
			$pos_b = rand(0, 3);

			$line = new DrawLine();

			for ($i = 0; $i <= 3; $i++) {
				$line->setAdjudicator($i, $this->adjudicators[$i]);
			}

			for ($i = 0; $i <= 3; $i++) {
				$line->setTeamOn($i, $this->teams[rand(1, count($this->teams) - 1)]);
			}

			$line = $this->algo->calcEnergyLevel($line);

			$line_a = clone $line;
			$line_b = clone $line;

			Debug::debug("PosA:" . $pos_a . "\tPosB: " . $pos_b);
			Debug::debug("");
			Debug::debug("A before: " . implode(", ", ArrayHelper::getColumn($line_a->getAdjudicators(), "id")));
			Debug::debug("B before: " . implode(", ", ArrayHelper::getColumn($line_b->getAdjudicators(), "id")));
			Debug::debug("");
			$return_lines = $this->algo->swap_adjudicator($line_a, $pos_a, $line_b, $pos_b);

			$line_a = $return_lines[0];
			$line_b = $return_lines[1];

			Debug::debug("A after:  " . implode(", ", ArrayHelper::getColumn($line_a->getAdjudicators(), "id")));
			Debug::debug("B after:  " . implode(", ", ArrayHelper::getColumn($line_b->getAdjudicators(), "id")));

			expect("A change:", $line_a->getAdjudicator($pos_a)["id"])->equals($line->getAdjudicator($pos_b)["id"]);
			expect("B change:", $line_b->getAdjudicator($pos_b)["id"])->equals($line->getAdjudicator($pos_a)["id"]);

		} catch (\Exception $ex) {
			throw new Exception($ex->getMessage() . " in " . $ex->getFile() . ":" . $ex->getLine());
		}
	}

	public function testEnergyCalculation()
	{
		try {
			$line = new DrawLine();


			for ($i = 0; $i <= 3; $i++) {
				$line->setAdjudicator($i, $this->adjudicators[$i + 1]);
			}

			for ($i = 0; $i <= 3; $i++) {
				$line->setTeamOn($i, $this->teams[$i + 1]);
			}

			$line = $this->algo->calcEnergyLevel($line);

			expect("Energy Level is set", $line->energyLevel)->notEquals(0);
		} catch (\Exception $ex) {
			throw new Exception($ex->getMessage() . " in " . $ex->getFile() . ":" . $ex->getLine() . "\n" . $ex->getTraceAsString());
		}
	}

	public function testEnergyCalcSwapRecalcEnergy()
	{
		Debug::debug("\n");
		try {
			$line = [];
			$pos_a = rand(0, 3);
			$pos_b = rand(0, 3);

			foreach (["a", "b"] as $prefix) {
				$line[$prefix] = new DrawLine(["id" => $prefix]);
				for ($i = 0; $i <= 3; $i++) {
					$line[$prefix]->setAdjudicator($i, $this->adjudicators[rand(1, count($this->adjudicators) - 1)]);
				}

				for ($i = 0; $i <= 3; $i++) {
					$line[$prefix]->setTeamOn($i, $this->teams[rand(1, count($this->teams) - 1)]);
				}

				$line[$prefix] = $this->algo->calcEnergyLevel($line[$prefix]);
			}

			$line_a_new = clone $line["a"];
			$line_b_new = clone $line["b"];

			$return_line = $this->algo->swap_adjudicator($line_a_new, $pos_a, $line_b_new, $pos_b);

			$line_a_new = $return_line[0];
			$line_b_new = $return_line[1];

			$line_a_new = $this->algo->calcEnergyLevel($line_a_new);
			$line_b_new = $this->algo->calcEnergyLevel($line_b_new);

			expect("Energy Change A", $line_a_new->energyLevel)->greaterThan(0);
			expect("Energy Change B", $line_b_new->energyLevel)->greaterThan(0);

		} catch (\Exception $ex) {
			throw new Exception($ex->getMessage() . " in " . $ex->getFile() . ":" . $ex->getLine());
		}
	}

	public function xtestRunFullDraw()
	{
		try {
			if (!($this->algo instanceof StrictWUDCRulesSOAS)) throw new Exception("Algo not set " . get_class($this->algo));

			$this->DRAW = $this->algo->makeDraw($this->venues, $this->teams, $this->adjudicators);

			expect("Energy", (intval($this->algo->best_energy)))->greaterThan(0);
			expect("Temperatur check:", $this->algo->temp)->lessThen(1.0);
			Debug::debug("\n");
			Debug::debug("Energy old:\t" . $this->algo->best_energy);
			Debug::debug("Temperatur old:\t" . Yii::$app->formatter->asDecimal($this->algo->temp, 10));

			expect("Not empty draw", count($this->DRAW))->greaterThan(0);
			Debug::debug("Number of rows: \t" . count($this->DRAW));
			expect("Amount of lines", count($this->DRAW))->equals(count($this->teams) / 4);

			Debug::debug("");
			foreach ($this->DRAW as $line) {
				//Debug::debug("Messages for room ".$line->venue["name"]);
				foreach ($line->messages as $m) {
					//Debug::debug("|- ".$m["key"]."\t".$m["msg"]."\t".$m["penalty"]);
				}
				$oldEnergyRooms[$line->venue["id"]] = $line->energyLevel;
				//Debug::debug("");
			}

			/************/

			$old_temp = $this->algo->temp;
			$old_energy = $this->algo->best_energy;
			$old_draw_energy = DrawLine::getDrawEnergy($this->DRAW);

			$this->DRAW = $this->algo->optimiseAdjudicatorAllocation(rand(1, 5000), $old_temp);

			$new_temp = $this->algo->temp;
			$new_energy = $this->algo->best_energy;
			$new_draw_energy = DrawLine::getDrawEnergy($this->DRAW);

			Debug::debug("Temperatur new:\t" . Yii::$app->formatter->asDecimal($this->algo->temp, 10));
			Debug::debug("");

			$calcImprovement = 0;
			foreach ($this->DRAW as $line) {
				Debug::debug($line->venue["name"] . " Change:");
				$old = $oldEnergyRooms[$line->venue["id"]];

				$improvement = ($old - $line->energyLevel);
				$calcImprovement += $improvement;
				Debug::debug("Old: " . $old . "\tNew: " . $line->energyLevel . "\tImproved: " . $improvement);
				Debug::debug("");

				//expect("Improvement", $improvement)->greaterOrEquals(0);
			}

			Debug::debug("---");
			Debug::debug("Overall Draw improvement: \t" . ($old_draw_energy - $new_draw_energy));
			Debug::debug("Best Energy\t old: " . $old_energy . "\tnew: " . $new_energy . "\tdifference: " . ($old_energy - $new_energy));

			expect("Temperatur check", $new_temp)->lessOrEquals($old_temp);
			expect("Energy", $new_draw_energy)->lessOrEquals($old_draw_energy);

		} catch (\Exception $ex) {
			throw new Exception($ex->getMessage() . " in " . $ex->getFile() . ":" . $ex->getLine());
		}
	}

	/**
	 * @inheritdoc
	 */
	public function fixtures()
	{
		return [
			'team'        => [
				'class'    => \tests\codeception\common\fixtures\TeamFixture::className(),
				'dataFile' => '@tests/codeception/common/fixtures/data/team.php',
			],
			'venue'       => [
				'class'    => \tests\codeception\common\fixtures\VenueFixture::className(),
				'dataFile' => '@tests/codeception/common/fixtures/data/venue.php',
			],
			'adjudicator' => [
				'class'    => \tests\codeception\common\fixtures\AdjudicatorFixture::className(),
				'dataFile' => '@tests/codeception/common/fixtures/data/adjudicator.php',
			],
			'tournament'  => [
				'class'    => \tests\codeception\common\fixtures\TournamentFixture::className(),
				'dataFile' => '@tests/codeception/common/fixtures/data/tournament.php',
			],
			'round'       => [
				'class'    => \tests\codeception\common\fixtures\RoundFixture::className(),
				'dataFile' => '@tests/codeception/common/fixtures/data/round.php',
			],
			'round'       => [
				'class'    => \tests\codeception\common\fixtures\RoundFixture::className(),
				'dataFile' => '@tests/codeception/common/fixtures/data/round.php',
			],
			'team'        => [
				'class'    => \tests\codeception\common\fixtures\EnergyConfigFixture::className(),
				'dataFile' => '@tests/codeception/common/fixtures/data/energyconfig.php',
			],
		];
	}

	protected function tearDown()
	{
		parent::tearDown();
	}

}
