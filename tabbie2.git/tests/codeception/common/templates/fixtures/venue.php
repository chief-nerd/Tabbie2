<?php

use Faker\Generator;

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'id' => ($index + 1),
    'tournament_id' => 1,
    'name' => "Room " . ($index + 1),
    'active' => ($faker->numberBetween(0, 100) < 20) ? 0 : 1,
];
