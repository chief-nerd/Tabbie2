<?php

namespace common\components\TabAlgorithmus;

use \common\components\TabAlgorithmus;
use common\models\Team;
use common\models\Venue;
use common\models\Adjudicator;
use yii\base\Exception;
use \Codeception\Util\Debug;
use common\models\DrawLine;
use Yii;

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

        /**
         * Sort Adjudicator at Strength
         */
        $adjudicators = $this->sort_adjudicators($adjudicators);

        /*
          First we need to make the brackets for each debate. This means ordering the teams by the number of points.
         */
        $teams = $this->sort_teams($teams);

        /*
          Then, within point brackets, we randomise the teams
         */
        $teams = $this->randomise_within_points($teams);

        /**
         * Set Past Position Matrix
         */
        for ($i = 0; $i < count($teams); $i++) {
            $teams[$i]->positionMatrix = $teams[$i]->getPastPositionMatrix();
        }

        /**
         * Generate a first rough draw by running the teams down from top to bottom and allocate them
         */
        for ($i = 0; $i < $active_rooms; $i++) {
            $line = new DrawLine();

            $choosen = array_splice($teams, 0, 4);
            shuffle($choosen);
            $line->setTeamsByArray($choosen);

            $line->venue = $venues[$i];
            $this->DRAW[] = $line;
        }


        /**
         * Now start improving that initial set
         * Go through the Draw until you can't make any improvements
         */
        $stillFoundASwap = true;
        while ($stillFoundASwap) {
            $stillFoundASwap = false; //Assume we are done, prove me wrong

            foreach ($this->DRAW as $line) {
                foreach ($line->teams as $pos => $team) {
                    if ($team->getPositionBadness($pos) > 0) { // Not optimal positioning exists here
                        if ($this->find_best_swap_for($line, $pos)) { //Do we find a swap that makes it better
                            $stillFoundASwap = true; //We found a better swap, do the loop again
                            break;
                        }
                    }
                }
                if ($stillFoundASwap)
                    break; //Found it already break on!
            }
            //If we havn't found a better swap $stillFoundASwap should be false and the loop breaks
        }

        /*
         * Allocate the Adjudicators
         */
        $lineID = 0;
        foreach ($adjudicators as $adj) {
            $this->DRAW[$lineID]->addAdjudicator($adj);

            if (isset($this->DRAW[$lineID + 1])) //Is there a next line
                $lineID++;
            else
                $lineID = 0; //Start at beginning
        }

        /*
         * We have found the best possible combination
         * There is no better swap possible now.
         * Return der DRAW[] and get ready to debate
         */
        return $this->DRAW;
    }

    /**
     * Sortiert Teams
     * @param Team[] $teams
     * @return Team[]
     */
    public function sort_teams($teams) {
        usort($teams, array('common\models\Team', 'compare_points'));
        return $teams;
    }

    /**
     * Sortiert Adjudicator
     * @param Adjudicator[] $adj
     * @return Adjudicator[]
     */
    public function sort_adjudicators($adj) {
        usort($adj, array('common\models\Adjudicator', 'compare_strength'));
        return $adj;
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
     * Swapps 2 Teams
     * @param DrawLine $line_a
     * @param integer $pos_a
     * @param DrawLine $line_b
     * @param integer $pos_b
     */
    public function swap_teams($line_a, $pos_a, $line_b, $pos_b) {

        $team_a = $line_a->getTeamOn($pos_a);
        $team_b = $line_b->getTeamOn($pos_b);

        $line_a->setTeamOn($pos_a, $team_b);
        $line_b->setTeamOn($pos_b, $team_a);
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

    /**
     *
     * @param integer $line
     * @param integer $pos_a
     * @return boolean
     */
    public function find_best_swap_for($line_a, $pos_a) {
        $team_a = $line_a->getTeamOn($pos_a);
        $best_effect = 0;
        $best_team_b_line = false;
        $best_team_b_pos = false;

        foreach ($this->DRAW as $line) {
            foreach ($line->getTeams() as $pos_b => $team_b) { //this loop especially can be limited
                if ($team_a->is_swappable_with($team_b)) {

                    //Get Status Quo Badness
                    $current = $team_a->getPositionBadness($pos_a) + $team_b->getPositionBadness($pos_b);
                    //How it would look like
                    $future = $team_a->getPositionBadness($pos_b) + $team_b->getPositionBadness($pos_a);

                    $net_effect = $future - $current;
                    if ($net_effect < $best_effect) {
                        $best_effect = $net_effect;
                        $best_team_b_line = $line;
                        $best_team_b_pos = $pos_b;
                    }
                }
            }
        }
        if ($best_team_b_line && $best_team_b_pos) {
            $this->swap_teams($line_a, $pos_a, $best_team_b_line, $best_team_b_pos);
            return true;
        }
        return false;
    }

}
