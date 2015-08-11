<?php

use Faker\Generator;

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
$city = $faker->city;
$date = $faker->unixTime;
return [
    'id' => ($index + 1),
    'url_slug' => str_replace(" ", "-", $city . "-IV-" . $faker->year),
    'name' => $city . " IV",
    'start_date' => date("Y-m-d H:i:s", $date),
    'end_date' => date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", $date) . " +3 days")),
    'logo' => $faker->optional()->file(),
    'time' => time(),
    'tabAlgorithmClass' => 'StrictWUDCRules',
];
