<?php

use Faker\Generator;
use common\models\Tournament;

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
$t = common\models\Tournament::findOne(["id" => 1]);
if ($t instanceof common\models\Tournament) {
    $draws = array();

    foreach ($t->getRounds()->all() as $r) {
        array_push($draws, [
            'tournament_id' => $t->id,
            'round_id' => $r->id,
            'time' => $faker->dateTime()->format('Y-m-d H:i:s'),
        ]);
    }
    return $draws;
} else
    throw new Exception("Tournament not found");
