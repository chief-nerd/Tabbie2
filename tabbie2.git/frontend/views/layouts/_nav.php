<?php
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;

NavBar::begin([
	'brandLabel' => Yii::$app->params["appName"],
	'brandUrl' => Yii::$app->homeUrl,
	'options' => [
		'class' => 'navbar-inverse navbar-fixed-top',
	],
]);
$menuItems = [
	['label' => 'Home', 'url' => ['/site/index']],
	['label' => 'About', 'url' => ['/site/about']],
	['label' => 'Tournaments', 'url' => ['tournament/index']],
];

if (Yii::$app->user->isAdmin()) {
	$menuItems[] = ['label' => 'Users', 'url' => ['user/index']];
}

if (Yii::$app->user->isGuest) {
	$menuItems[] = ['label' => 'Signup', 'url' => ['/site/signup']];
	$menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
}
else {
	$menuItems[] = [
		'label' => Yii::$app->user->getModel()->surename . "'s Profile",
		'url' => ['user/view', 'id' => Yii::$app->user->id],
	];
	$menuItems[] = [
		'label' => Yii::$app->user->getModel()->surename . "'s History",
		'url' => ['user/history', 'id' => Yii::$app->user->id],
	];
	$menuItems[] = [
		'label' => 'Logout',
		'url' => ['/site/logout'],
		'linkOptions' => ['data-method' => 'post']
	];
}
echo Nav::widget([
	'options' => ['class' => 'navbar-nav menu navbar-right'],
	'items' => $menuItems,
]);
NavBar::end();