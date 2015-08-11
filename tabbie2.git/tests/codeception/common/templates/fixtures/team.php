<?php

use Faker\Generator;

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
	'id'               => ($index + 1),
	'name'             => "Team " . $faker->colorName,
	'active'           => $faker->boolean(80),
	'tournament_id'    => 1,
	'speakerA_id'      => $faker->unique()->numberBetween(1, 50),
	'speakerB_id'      => $faker->unique()->numberBetween(1, 50),
	'society_id'       => $faker->numberBetween(1, 10),
	'points'           => $faker->numberBetween(0, 27),
	'isSwing'          => $faker->boolean(10),
	'language_status'  => $faker->numberBetween(\common\models\User::LANGUAGE_NONE, \common\models\User::LANGUAGE_INTERVIEW),
	'speakerA_speaks'  => $faker->numberBetween(65, 300),
	'speakerB_speaks'  => $faker->numberBetween(65, 300),
	'speakerA_checkin' => $faker->boolean(50),
	'speakerB_checkin' => $faker->boolean(50),
];
