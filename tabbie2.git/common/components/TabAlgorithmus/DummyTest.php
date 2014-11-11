<?php

namespace common\components\TabAlgorithmus;

use \common\components\TabAlgorithmus;

class DummyTest extends TabAlgorithmus {

    public function makeDraw($venues, $teams, $adjudicators, $preset_panels = null, $strikes = null) {
        $draw = [];
        $a = 0;
        $v = 0;
        $line = 0;
        if (count($teams) % 4 != 0)
            throw new Exception("Amount of Teams must be divided by 4 ;)", "500");

        for ($i = 0; $i <= count($teams); $i + 4) {

            if (isset($adjudicators[$a])) {
                $chair = $adjudicators[$a];
            } else {
                //We are missing a chair -> take last wing in panel > 1
                for ($last = (count($draw) - 1); $last > 0; $last--) {
                    $prevPanel = $draw[$last]->panel;
                    if (count($prevPanel) > 1) { //Don't reset chair
                        $chair = $prevPanel->panel[count($prevPanel->panel) - 1];
                        unset($prevPanel->panel[count($prevPanel->panel) - 1]);
                    }
                }
            }

            $draw[$line] = [
                "venue" => $venues[$v],
                "og" => $teams[$i],
                "oo" => $teams[$i + 1],
                "cg" => $teams[$i + 2],
                "co" => $teams[$i + 3],
                "panel" => [
                    "chair" => $chair,
                    (isset($adjudicators[$a + 1]) ? $adjudicators[$a + 1] : null),
                ]
            ];

            if (isset($adjudicators[$a + 1]))
                $draw[$line - 1]->panel[] = $adjudicators[$a + 1];
            if (isset($adjudicators[$a + 2]))
                $draw[$line - 1]->panel[] = $adjudicators[$a + 2];

            $line++;
            $v++;
            $a = $a + 3;
        }

        return $draw;
    }

}
