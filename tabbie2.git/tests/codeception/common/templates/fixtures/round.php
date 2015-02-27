<?php

use Faker\Generator;

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'number' => ($index + 1),
    'tournament_id' => 1,
    'motion' => "This house would " . $faker->sentence($faker->numberBetween(6, 20)),
    'infoslide' => $faker->optional()->sentence($faker->numberBetween(20, 50)),
    'time' => time(),
    'published' => 0,
    'displayed' => 0,
    'closed' => 0,
    'prep_started' => null,
    'finished_time' => null,
];
