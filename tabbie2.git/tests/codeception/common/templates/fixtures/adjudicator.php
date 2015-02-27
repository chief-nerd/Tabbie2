<?php

use Faker\Generator;

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'id' => ($index + 1),
    'tournament_id' => 1,
    'user_id' => $faker->unique()->numberBetween(1, 50),
    'strength' => $faker->numberBetween(0, 9),
    'society_id' => $faker->numberBetween(1, 10),
];
