<?php

use Faker\Generator;

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
$city = $faker->city;
$name = "University of " . $city;
return [
    'id' => ($index + 1),
    'fullname' => $name,
    'abr' => common\models\Society::generateAbr($name) . $i,
    'city' => $city,
    'country' => $faker->country,
];
