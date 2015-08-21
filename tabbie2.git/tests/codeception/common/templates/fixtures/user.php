<?php

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
$security = Yii::$app->getSecurity();
$user = new \common\models\User([
	"givenname" => $faker->firstName,
	"surename"  => $faker->lastName,
]);
$x = $faker->numberBetween(0, 10);
return [
	'id'        => $index,
	'url_slug'  => $user->generateUrlSlug(),
    'auth_key' => $security->generateRandomString(),
	//password_0
    'password_hash' => $security->generatePasswordHash('password_' . $index),
    'password_reset_token' => $security->generateRandomString() . '_' . time(),
	'email'     => $faker->unique()->email,
    'role' => 10,
    'status' => 10,
	'givenname' => $user->givenname,
	'surename'  => $user->surename,
	'picture'   => $faker->optional()->imageUrl(150, 150),
	'last_change' => date("Y-m-d H:i:s", strtotime(time() . " + $x days")),
	'time'        => date("Y-m-d H:i:s", time()),
];
