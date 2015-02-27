<?php

use Faker\Generator;

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
$team = common\models\Team::findAll(["tournament_id" => 1]);
$draw = array();
foreach ($team as $t)
    $draw[] = [
        'draw_id' => 1,
        'team_id' => $t->id,
        'result_id' => $faker->numberBetween(0, 100),
        'points' => $faker->numberBetween(0, 27),
        'speakerA_speaks' => $faker->numberBetween(50, 85),
        'speakerB_speaks' => $faker->numberBetween(50, 85),
    ];

return $draw;

