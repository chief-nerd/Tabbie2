<?php

use Faker\Generator;

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
$city = $faker->city;
return [
    'id' => ($index + 1),
    'fullname' => "University of " . $city,
    'abr' => strtoupper($faker->lexify("????")),
    'city' => $city,
    'country' => $faker->country,
];
