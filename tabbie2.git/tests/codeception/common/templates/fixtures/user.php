<?php

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
$security = Yii::$app->getSecurity();

return [
    'username' => $faker->userName,
    'auth_key' => $security->generateRandomString(),
    //password_0
    'password_hash' => $security->generatePasswordHash('password_' . $index),
    'password_reset_token' => $security->generateRandomString() . '_' . time(),
    'email' => $faker->email,
    'role' => 10,
    'status' => 10,
    'givenname' => $faker->firstName,
    'surename' => $faker->lastName,
    'picture' => null,
    'last_change' => time(),
    'time' => time(),
];
