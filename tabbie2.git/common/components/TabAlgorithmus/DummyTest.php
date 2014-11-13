<?php

namespace common\components\TabAlgorithmus;

use \common\components\TabAlgorithmus;
use yii\base\Exception;

class DummyTest extends TabAlgorithmus {

    public function makeDraw($venues, $teams, $adjudicators, $preset_panels = null, $strikes = null) {

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

        for ($iterateTeam = 0; $iterateTeam < count($teams); $iterateTeam = $iterateTeam + 4) {

            if (isset($adjudicators[$iterateAdj + 1])) {
                $iterateAdj++;
                $chair = $adjudicators[$iterateAdj];
            } else {
//We are missing a chair -> take last wing in panel > 1
                for ($last = (count($draw) - 1); $last > 0; $last--) {
                    $prevPanel = $draw[$last]["panel"];
                    if (count($prevPanel) > 1) { //Don't reset chair
                        $chair = $prevPanel[count($prevPanel) - 1];
                        unset($prevPanel[count($prevPanel) - 1]);
                    }
                }
            }

            $draw[$line] = [
                "venue" => $venues[$iterateVenue],
                "og" => $teams[$iterateTeam],
                "oo" => $teams[$iterateTeam + 1],
                "cg" => $teams[$iterateTeam + 2],
                "co" => $teams[$iterateTeam + 3],
                "panel" => [
                    "chair" => $chair,
                    "strength" => 1,
                ]
            ];

            if (isset($adjudicators[$iterateAdj + 1])) {
                $iterateAdj++;
                $draw[$line]["panel"][] = $adjudicators[$iterateAdj];
            }
            if (isset($adjudicators[$iterateAdj + 1])) {
                $iterateAdj++;
                $draw[$line]["panel"][] = $adjudicators[$iterateAdj];
            }

            $line++;
            $iterateVenue++;
        }

        return $draw;
    }

}
