<?php

use Faker\Generator;

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'id' => ($index + 1),
    'name' => "Team " . ($index + 1),
    'tournament_id' => 1,
    'speakerA_id' => $faker->unique()->numberBetween(1, 50),
    'speakerB_id' => $faker->unique()->numberBetween(1, 50),
    'society_id' => $faker->numberBetween(1, 10),
	'points' => $faker->numberBetween(0, 27),
	'speakerA_speaks' => $faker->numberBetween(65, 300),
	'speakerB_speaks' => $faker->numberBetween(65, 300),
];
