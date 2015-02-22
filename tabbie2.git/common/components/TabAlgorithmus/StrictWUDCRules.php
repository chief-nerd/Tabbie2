<?php

namespace common\components\TabAlgorithmus;

use \common\components\TabAlgorithmus;

class StrictWUDCRules extends TabAlgorithmus {

    public function makeDraw($venues, $teams, $adjudicators, $preset_panels) {

        if (count($teams) % 4 != 0)
            throw new Exception("Amount of Teams must be divided by 4 ;)", "500");
        if ((count($teams) / 4) > count($venues))
            throw new Exception("Not enough active Rooms", "500");
        if (count($venues) > count($adjudicators))
            throw new Exception("Not enough adjudicators", "500");

        $draw = [];
        $iterateAdj = -1; //Because of ++
        $iterateVenue = 0;
        $line = 0;

        /*
          First we need to make the brackets for each debate. This means ordering the teams by the number of points.
         */

        $teams->sort_by_points;

        /*
          Then, within point brackets, we randomise the teams
         */

        $teams->randomise_within_points;

        /*
          Then do the first pass of allocations of the teams, which is not used as the draw, only as the seed. Firstly, make a temporary area with no positions.
         */

        $temp_debates = array();

        for ($i = 0; $i < count($teams) / 4; $i++) {
            $new_debate = array();
            for ($j = 0; $j < 4; $j++) {
                $debate[] = $teams[$i * 4 + $j];
            }
            $temp_debates[] = $new_debate;
        }

        /*
          Then get the characteristics of the debates for shufflin' purposes.
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
          Now go back to the ordered list, and we can start moving teams around.
         */


        /*
          Helper function to determine whether teams COULD replace each other in the same bracket (are they in the same bracket, or is one a pull up / down from their bracket?)
         */

        function is_swappable($team_a, $team_b) {
            $result = ($team_a["team_id"] != $team_b["team_id"]) &&
                    (($team_a["points"] == $team_b["points"]) ||
                    ($team_a["debate_level"] == $team_b["debate_level"]));
            return $result;
        }

        /*
          Helper function to swap two teams
         */

        function swap_two_teams(&$teams, &$team_a, &$team_b) {
            $current_position_a = $team_a["current_position"];
            $debate_level_a = $team_a["debate_level"];
            $index_a = $team_a["index"];

            $team_a["current_position"] = $team_b["current_position"];
            $team_a["debate_level"] = $team_b["debate_level"];
            $team_a["index"] = $team_b["index"];

            $team_b["current_position"] = $current_position_a;
            $team_b["debate_level"] = $debate_level_a;
            $team_b["index"] = $index_a;

            $teams[$team_a["index"]] = $team_a;
            $teams[$team_b["index"]] = $team_b;
        }

        /*
          Function to shuffle most of the teams around 50000 times, where applicable. This looks like it's superfluous given the next stage, but it's so we start off with a random distribution of teams.
         */

        function callard_shuffle(&$teams) {
            for ($i = 0; $i < 50000; $i++) {
                $team_a = $teams[array_rand($teams)];
                $team_b = $teams[array_rand($teams)];
                if (is_swappable($team_a, $team_b)) {
                    swap_two_teams($teams, $team_a, $team_b);
                }
            }
        }

        /*
          Every Day I'm Shufflin' (ELKE DAG IM SHUFFLIN)!
         */
        callard_shuffle();

        /*
          Now, we put teams into the positions where they are best suited.
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
         */
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

        /*
          Now, put them all in the structure, as required.
         */

        $draw = array();

        $draw[$line] = [
            "venue" => $venues[$iterateVenue],
            "og" => $teams[$iterateTeam],
            "oo" => $teams[$iterateTeam + 1],
            "cg" => $teams[$iterateTeam + 2],
            "co" => $teams[$iterateTeam + 3],
            "panel" => [
                "chair" => "",
                "strength" => "",
            ]
        ];

        $line++;
        $iterateVenue++;

        return $draw;
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

}
