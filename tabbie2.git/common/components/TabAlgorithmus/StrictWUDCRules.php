<?php

namespace common\components\TabAlgorithmus;

use \common\components\TabAlgorithmus;
use common\models\Team;
use common\models\Venue;
use common\models\Adjudicator;
use yii\base\Exception;
use \Codeception\Util\Debug;
use common\models\DrawLine;

class StrictWUDCRules extends TabAlgorithmus {

    /**
     * Function to calculate a draw based on WUDC strict Rules
     * @param Venue[] $venues
     * @param Team[] $teams
     * @param type $adjudicators
     * @param type $preset_panels
     * @return type
     * @throws Exception
     */
    public function makeDraw($venues, $teams, $adjudicators, $preset_panels = array()) {

        /**
         * The Draw
         */
        $DRAW = array();

        $active_rooms = (count($teams) / 4);
        if (count($teams) % 4 != 0)
            throw new Exception("Amount of active Teams must be divided by 4 ;) - (active: " . count($teams) . ")", "500");
        if ($active_rooms > count($venues))
            throw new Exception("Not enough active Rooms (active:" . count($venues) . " required:" . $active_rooms . ")", "500");
        if ($active_rooms > count($adjudicators))
            throw new Exception("Not enough adjudicators (active:" . count($adjudicators) . " min-required:" . $active_rooms . ")", "500");

        /**
         * Shuffle venues
         */
        shuffle($venues);

        /*
          First we need to make the brackets for each debate. This means ordering the teams by the number of points.
         */
        $teams = $this->sort_teams($teams);
        $this->debug($teams);

        /*
          Then, within point brackets, we randomise the teams
         */
        $teams = $this->randomise_within_points($teams);
        $this->debug($teams);

        /**
         * Generate a first rough draw by running the teams down from top to bottom
         */
        for ($i = 0; $i < $active_rooms; $i++) {
            $line = new DrawLine();
            $line->setTeams($teams[$i * 4], $teams[$i * 4 + 1], $teams[$i * 4 + 2], $teams[$i * 4 + 3]);
            $line->venue = $venues[$i];
            $line->setChair($adjudicators[$i]);
            $DRAW[] = $line;
        }

        /*
          Then get the characteristics of the debates for shufflin' purposes.
         * 3 things
         * current position OG, OO, CG, CO
         * Beste team in the Debate - how many points
         * Value of a Room is classified
         */

        function get_debate_characteristics(&$debates) {
            $result = array();
            $index = 0;
            foreach ($debates as $debate) {
                $current_position = 0;
                $best_team = $debate[0];
                $debate_level = $best_team["points"];
                foreach ($debate as $team) {
                    $attributed_team = $team;
                    $attributed_team["current_position"] = $current_position;
                    $attributed_team["debate_level"] = $debate_level;
                    $attributed_team["index"] = $index;
                    $result[] = $attributed_team;
                    $current_position++;
                    $index++;
                }
            }
            return $result;
        }

        get_debate_characteristics($temp_debates);

        /*
          Now, we put teams into the positions where they are best suited.
         * Run through all teams trying to find the best swap that positional rotation
         */

        function find_best_swap_for(&$teams, &$team_a) {
            $best_effect = 0;
            $best_team_b = false;
            foreach ($teams as $team_b) { //this loop especially can be limited
                if (is_swappable($team_a, $team_b)) {
                    $current = team_badness($team_a) + team_badness($team_b);
                    $future = team_badness($team_a, $team_b["current_position"]) + team_badness($team_b, $team_a["current_position"]);

                    $net_effect = $future - $current;
                    if ($net_effect < $best_effect) {
                        $best_effect = $net_effect;
                        $best_team_b = $team_b;
                    }
                }
            }
            if ($best_team_b) {
                swap_two_teams($teams, $team_a, $best_team_b);
                return true;
            }
            return false;
        }

        function team_badness(&$team, $position = -1) {
            $result = 0;
            $positions = $team["positions"];
            if ($position == -1)
                $position = $team["current_position"];
            $positions[position_to_s($position)] += 1;
            return badness($positions);
        }

        function position_to_s($i) {
            if ($i == 0)
                return "og";
            if ($i == 1)
                return "oo";
            if ($i == 2)
                return "cg";
            if ($i == 3)
                return "co";
            return "trouble";
        }

        /*
          Then move the teams around in accordance with the randomiser algorithm, to put them all in the most appropriate positions.
         */
        $previous_solution = 0;
        while (teams_badness($teams) > 0) {
            if ($previous_solution == teams_badness($teams))
                break;
            $previous_solution = teams_badness($teams);
            foreach ($teams as $team)
                if (team_badness($team) > 0)
                    if (find_best_swap_for($teams, $team))
                        break;
        }


        /*
          This file is generated automatically by maths. Editing it manually is considered very stupid. Please don't. Especially Calum. Also Molly. Richard, you're okay ;).
          Positional rotation
         *
         * */
        $badness_lookup = array(
            "0, 0, 0, 0" => 0,
            "0, 0, 0, 1" => 0,
            "0, 0, 0, 2" => 4,
            "0, 0, 0, 3" => 36,
            "0, 0, 0, 4" => 144,
            "0, 0, 0, 5" => 324,
            "0, 0, 0, 6" => 676,
            "0, 0, 0, 7" => 1296,
            "0, 0, 0, 8" => 2304,
            "0, 0, 0, 9" => 3600,
            "0, 0, 1, 1" => 0,
            "0, 0, 1, 2" => 4,
            "0, 0, 1, 3" => 36,
            "0, 0, 1, 4" => 100,
            "0, 0, 1, 5" => 256,
            "0, 0, 1, 6" => 576,
            "0, 0, 1, 7" => 1156,
            "0, 0, 1, 8" => 1936,
            "0, 0, 2, 2" => 16,
            "0, 0, 2, 3" => 36,
            "0, 0, 2, 4" => 100,
            "0, 0, 2, 5" => 256,
            "0, 0, 2, 6" => 576,
            "0, 0, 2, 7" => 1024,
            "0, 0, 3, 3" => 64,
            "0, 0, 3, 4" => 144,
            "0, 0, 3, 5" => 324,
            "0, 0, 3, 6" => 576,
            "0, 0, 4, 4" => 256,
            "0, 0, 4, 5" => 400,
            "0, 1, 1, 1" => 0,
            "0, 1, 1, 2" => 4,
            "0, 1, 1, 3" => 16,
            "0, 1, 1, 4" => 64,
            "0, 1, 1, 5" => 196,
            "0, 1, 1, 6" => 484,
            "0, 1, 1, 7" => 900,
            "0, 1, 2, 2" => 4,
            "0, 1, 2, 3" => 16,
            "0, 1, 2, 4" => 64,
            "0, 1, 2, 5" => 196,
            "0, 1, 2, 6" => 400,
            "0, 1, 3, 3" => 36,
            "0, 1, 3, 4" => 100,
            "0, 1, 3, 5" => 196,
            "0, 1, 4, 4" => 144,
            "0, 2, 2, 2" => 4,
            "0, 2, 2, 3" => 16,
            "0, 2, 2, 4" => 64,
            "0, 2, 2, 5" => 144,
            "0, 2, 3, 3" => 36,
            "0, 2, 3, 4" => 64,
            "0, 3, 3, 3" => 36,
            "1, 1, 1, 1" => 0,
            "1, 1, 1, 2" => 0,
            "1, 1, 1, 3" => 4,
            "1, 1, 1, 4" => 36,
            "1, 1, 1, 5" => 144,
            "1, 1, 1, 6" => 324,
            "1, 1, 2, 2" => 0,
            "1, 1, 2, 3" => 4,
            "1, 1, 2, 4" => 36,
            "1, 1, 2, 5" => 100,
            "1, 1, 3, 3" => 16,
            "1, 1, 3, 4" => 36,
            "1, 2, 2, 2" => 0,
            "1, 2, 2, 3" => 4,
            "1, 2, 2, 4" => 16,
            "1, 2, 3, 3" => 4,
            "2, 2, 2, 2" => 0,
            "2, 2, 2, 3" => 0
        );

        function badness($positions) {
            global $badness_lookup;
            sort($positions);
            while ($positions[0] + $positions[1] + $positions[2] + $positions[3] >= 10) {
                for ($i = 0; $i < 4; $i++)
                    $positions[$i] = max(0, $positions[$i] - 1);
            }
            return $badness_lookup["{$positions[0]}, {$positions[1]}, {$positions[2]}, {$positions[3]}"];
        }

        return $DRAW;
    }

    /**
     * Sortiert Teams
     * @param Team[] $teams
     * @return Team[]
     */
    public function sort_teams($teams) {
        usort($teams, array('common\models\Team', 'sort_points'));
        return $teams;
    }

    /**
     * Randomises the Teams within Teampoints
     * @param Team[] $teams
     * @return Team[]
     */
    public function randomise_within_points($teams) {

        $saved_points = $teams[0]->getPoints(); //reset to start
        $last_break = 0;

        for ($i = 0; $i < count($teams); $i++) {
            $team_points = $teams[$i]->getPoints();
            if ($team_points != $saved_points) {
                $bracket = array_slice($teams, $last_break, ($i - $last_break));
                shuffle($bracket);
                array_splice($teams, $last_break, ($i - $last_break), $bracket);

                $last_break = $i;
                $saved_points = $team_points;
            }
        }
        return $teams;
    }

    /**
     * Swapps 2 Teams in the Teams[]
     * @param Team[] $teams
     * @param Team $team_a
     * @param Team $team_b
     */
    public function swap_teams(&$teams, $team_a, $team_b) {

        $index_a = array_search($team_a, $teams);
        $index_b = array_search($team_b, $teams);

        if ($index_a && $index_b) {
            $teams[$index_a] = $team_b;
            $teams[$index_b] = $team_a;
        } else
            throw new Exception("One of the Team was not found in Teams Array");
    }

    /**
     *
     * @param Debate $debate
     * @param type $name Description
     * @return boolean
     */
    public function calcEnergyLevel($debate) {
        $tournament = $debate->tournament;
        $debate->energy = rand(1, 100);
        return true;
    }

    /**
     * Sets up the variables in the EnergyConfig
     * @param \common\models\Tournament $tournament
     */
    public function setup($tournament) {
        $tid = $tournament->id;
        $strike = new \common\models\EnergyConfig();
        $strike->tournament_id = $tid;
        $strike->label = "Strike Penalty";
        $strike->key = "strike";
        $strike->value = -1000;
        $strike->save();
        return true;
    }

    /**
     * Wrapper to produce easier debug infos in codeception
     * @param type $teams
     */
    private function debug($teams) {
        $output = "";
        foreach ($teams as $t) {
            $output .= $t->id . "/" . $t->getPoints() . "\t";
        }

        Debug::debug($output);
    }

}
