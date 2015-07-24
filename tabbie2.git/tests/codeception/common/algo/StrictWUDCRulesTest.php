<?php

namespace tests\codeception\common\unit\components;

use algorithms\algorithms\StrictWUDCRules;
use common\models\EnergyConfig;
use common\models\Tournament;
use Yii;
use tests\codeception\common\unit\DbTestCase;
use Codeception\Specify;
use Codeception\Verify;
use common\components\TabAlgorithmus;
use Codeception\Util\Debug;
use common\models\Team;
use common\models\Venue;
use common\models\Adjudicator;
use yii\base\Exception;

/**
 * Test StrictWUDCRules Class
 */
class StrictWUDCRulesTest extends DbTestCase {

	/**
	 * @var StrictWUDCRules
	 */
	public $algo = false;

    public function setUp() {
        parent::setUp();
        $tournament = \common\models\Tournament::findByPk(1);
		if (!$tournament instanceof Tournament) throw new Exception("Setup fail");
        $this->algo = $tournament->getTabAlgorithmInstance();
		$this->algo->tournament_id = $tournament->id;
		$this->algo->energyConfig = EnergyConfig::loadArray($tournament->id);
		$this->algo->round_number = 1;
    }

    protected function tearDown() {
        parent::tearDown();
    }

    public function testSortAdjudicators() {
        $adj = \common\models\Adjudicator::findAll(["tournament_id" => 1]);
		if ($this->algo === false) {
			throw new Exception("Setup fail");
		}
        $new_adj = $this->algo->sort_adjudicators($adj);

        expect("Preserve Amount", count($new_adj))->equals(count($adj));
        for ($i = 0; $i < count($new_adj); $i++) {
            /* @var $t Team */
            $a = $new_adj[$i];

            $this->assertInstanceOf(Adjudicator::className(), $a);
            if ($i > 0) {
                expect("Sort Order", $a->strength)->lessOrEquals($new_adj[$i - 1]->strength);
            }
        }
    }

    public function testSortAndRandomisationOfTeams() {
	    $teams = \common\models\Team::find()->tournament(1)->all();
        $new_teams = $this->algo->sort_teams($teams);
        $new_teams = $this->algo->randomise_within_points($new_teams);

        expect("Preserve Amount", count($new_teams))->equals(count($teams));
        for ($i = 0; $i < count($new_teams); $i++) {
	        $this->assertInstanceOf(Team::className(), $new_teams[$i]);
            if ($i > 0) {
	            expect("Sort Order", $new_teams[$i]->points)->lessOrEquals($new_teams[$i - 1]->points);
            }
        }
    }

	public function testSwapTeams() {

        $pos_a = rand(0, 3);
        $team_a = new Team(["id" => 1]);
        $line_a = new \common\models\DrawLine();
        $line_a->setTeamOn($pos_a, $team_a);
        $team_a = new Team(["id" => 1]);

        $pos_b = rand(0, 3);
        $team_b = new Team(["id" => 2]);
        $line_b = new \common\models\DrawLine();
        $line_b->setTeamOn($pos_b, $team_b);

        $this->algo->swap_teams($line_a, $pos_a, $line_b, $pos_b);

        expect($line_a->getTeamOn($pos_a))->equals($team_b);
        expect($line_b->getTeamOn($pos_b))->equals($team_a);
    }

	public function testSwapAdjudicator() {

		$pos_a = rand(0, 3);
		$adju_a = new Adjudicator(["id" => 1]);
		$line_a = new \common\models\DrawLine();
		$line_a->addAdjudicator($adju_a);

		$pos_b = rand(0, 3);
		$adju_b = new Adjudicator(["id" => 2]);
		$line_b = new \common\models\DrawLine();
		$line_b->addAdjudicator($adju_b);

		$this->algo->swap_adjudicator($line_a, $pos_a, $line_b, $pos_b);

		expect($line_a->getAdjudicator($pos_a))->equals($adju_b);
		expect($line_b->getAdjudicator($pos_b))->equals($adju_a);
	}

    public function testRunFullDraw() {

	    $venues = Venue::find()->active()->tournament(1)->asArray()->all();
	    $teams = Team::find()->active()->tournament(1)->asArray()->all();

	    $adjudicators_Query = Adjudicator::find()->active()->tournament(1);

	    $adjudicatorsObjects = $adjudicators_Query->all();
	    $adjudicators = [];
	    for ($i = 0; $i < count($adjudicatorsObjects); $i++) {
		    $adjudicators[$i] = $adjudicatorsObjects[$i]->attributes;
		    $adjudicators[$i]["name"] = $adjudicatorsObjects[$i]->name;

		    $strikedAdju = $adjudicatorsObjects[$i]->getStrikedAdjudicators()->select(["id"])->asArray()->all();
		    $adjudicators[$i]["strikedAdjudicators"] = $strikedAdju;

		    $strikedTeam = $adjudicatorsObjects[$i]->getStrikedTeams()->select(["id", "name"])->asArray()->all();
		    $adjudicators[$i]["strikedTeams"] = $strikedTeam;

		    $adjudicators[$i]["pastAdjudicatorIDs"] = $adjudicatorsObjects[$i]->getPastAdjudicatorIDs();
		    $adjudicators[$i]["pastTeamIDs"] = $adjudicatorsObjects[$i]->getPastTeamIDs();
	    }


	    $adjudicators_strengthArray = ArrayHelper::getColumn(
		    $adjudicators_Query->select("strength")->asArray()->all(),
		    "strength"
	    );

	    /* Setup */
	    $this->algo->tournament_id = 1;
	    $this->algo->round_number = 1;
	    $this->algo->average_adjudicator_strength = array_sum($adjudicators_strengthArray) / count($adjudicators_strengthArray);
	    $this->algo->SD_of_adjudicators = $this->stats_standard_deviation($adjudicators_strengthArray);

	    $this->algo->makeDraw($venues, $teams, $adjudicators);
    }

    public function testEnergyCalculation() {
        $line = new \common\models\DrawLine();
	    $line = $this->algo->calcEnergyLevel($line);

		expect("Energy Level is set", $line->energyLevel)->notEquals(0);
    }

    /**
     * @inheritdoc
     */
    public function fixtures() {
        return [
            'team' => [
                'class' => \tests\codeception\common\fixtures\TeamFixture::className(),
                'dataFile' => '@tests/codeception/common/fixtures/data/team.php',
            ],
            'venue' => [
                'class' => \tests\codeception\common\fixtures\VenueFixture::className(),
                'dataFile' => '@tests/codeception/common/fixtures/data/venue.php',
            ],
            'adjudicator' => [
                'class' => \tests\codeception\common\fixtures\AdjudicatorFixture::className(),
                'dataFile' => '@tests/codeception/common/fixtures/data/adjudicator.php',
            ],
            'tournament' => [
                'class' => \tests\codeception\common\fixtures\TournamentFixture::className(),
                'dataFile' => '@tests/codeception/common/fixtures/data/tournament.php',
            ],
            'round' => [
                'class' => \tests\codeception\common\fixtures\RoundFixture::className(),
                'dataFile' => '@tests/codeception/common/fixtures/data/round.php',
            ],
            'round' => [
                'class' => \tests\codeception\common\fixtures\RoundFixture::className(),
                'dataFile' => '@tests/codeception/common/fixtures/data/round.php',
            ],
        ];
    }

}
