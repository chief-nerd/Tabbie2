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
	['label' => Yii::t("app", 'Home'), 'url' => ['/site/index']],
	['label' => Yii::t("app", 'About'), 'url' => ['/site/about']],
	['label' => Yii::t("app", 'Tournaments'), 'url' => ['tournament/index']],
];

if (Yii::$app->user->isAdmin()) {
	$menuItems[] = ['label' => Yii::t("app", 'Users'), 'url' => ['user/index']];
}

if (Yii::$app->user->isGuest) {
	$menuItems[] = ['label' => Yii::t("app", 'Signup'), 'url' => ['/site/signup']];
	$menuItems[] = ['label' => Yii::t("app", 'Login'), 'url' => ['/site/login']];
}
else {
	$menuItems[] = [
		'label' => Yii::t("app", "{user}'s Profile", ["user" => Yii::$app->user->getModel()->surename]),
		'url' => ['user/view', 'id' => Yii::$app->user->id],
	];
	$menuItems[] = [
		'label' => Yii::t("app", "{user}'s History", ["user" => Yii::$app->user->getModel()->surename]),
		'url' => ['history/index', 'user_id' => Yii::$app->user->id],
	];
	$menuItems[] = [
		'label' => Yii::t("app", 'Logout'),
		'url' => ['/site/logout'],
		'linkOptions' => ['data-method' => 'post']
	];
}
echo Nav::widget([
	'options' => ['class' => 'navbar-nav menu navbar-right'],
	'items' => $menuItems,
]);
NavBar::end();