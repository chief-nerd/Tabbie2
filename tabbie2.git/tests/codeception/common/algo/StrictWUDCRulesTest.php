<?php

namespace tests\codeception\common\unit\components;

use Yii;
use tests\codeception\common\unit\DbTestCase;
use Codeception\Specify;
use Codeception\Verify;
use common\components\TabAlgorithmus;
use Codeception\Util\Debug;
use common\models\Team;
use common\models\Venue;
use common\models\Adjudicator;

/**
 * Test StrictWUDCRules Class
 */
class StrictWUDCRulesTest extends DbTestCase {

    public $algo;

    public function setUp() {
        parent::setUp();
        $tournament = \common\models\Tournament::findByPk(1);
        $this->algo = $tournament->getTabAlgorithmInstance();
    }

    protected function tearDown() {
        parent::tearDown();
    }

    public function testSortAdjudicators() {
        $adj = \common\models\Adjudicator::findAll(["tournament_id" => 1]);
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
        $teams = \common\models\Team::findAll(["tournament_id" => 1]);
        $new_teams = $this->algo->sort_teams($teams);
        $new_teams = $this->algo->randomise_within_points($new_teams);

        expect("Preserve Amount", count($new_teams))->equals(count($teams));
        for ($i = 0; $i < count($new_teams); $i++) {
            /* @var $t Team */
            $t = $new_teams[$i];

            $this->assertInstanceOf(Team::className(), $t);
            if ($i > 0) {
                expect("Sort Order", $t->getPoints())->lessOrEquals($new_teams[$i - 1]->getPoints());
            }
        }
    }

    public function testSwap() {

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

    public function testRunFullDraw() {
        $venues = Venue::findAll(["tournament_id" => 1]);
        $teams = Team::findAll(["tournament_id" => 1]);
        $adjudicators = Adjudicator::findAll(["tournament_id" => 1]);
        $draw = $this->algo->makeDraw($venues, $teams, $adjudicators);
    }

    public function testEnergyCalculation() {
        $line = new \common\models\DrawLine();
        $round = \common\models\Round::findOne(1);
        $line = $this->algo->calcEnergyLevel($line, $round);
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
