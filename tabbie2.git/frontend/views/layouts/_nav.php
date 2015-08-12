<?php
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use kartik\helpers\Html;
use yii\bootstrap\Modal;

//$logoPath = Yii::getAlias("@frontend/assets/images/") . "Tabbie2LogoIcon.png";
//$logo =  Yii::$app->assetManager->publish($logoPath)[1];

NavBar::begin([
	'brandLabel' => Html::tag("img", "", ["src" => Yii::$app->params["base64_logo"], "alt" => Yii::$app->params["appName"] . " Logo"]) . "&nbsp;" . Yii::$app->params["appName"],
	'brandUrl' => Yii::$app->homeUrl,
	'options'  => [
		'class' => 'navbar-inverse navbar-fixed-top',
	],
]);
$menuItems = [
	['label' => Yii::t("app", 'About'), 'url' => ['/site/about']],
	['label' => Yii::t("app", 'How-To'), 'url' => ['/site/how-to']],
	['label' => Html::icon("calendar") . "&nbsp;" . Yii::t("app", 'Tournaments'), 'url' => ['tournament/index']],
];

if (Yii::$app->user->isAdmin()) {
	$menuItems[] = ['label' => Html::icon("globe") . "&nbsp;" . Yii::t("app", 'Users'), 'url' => ['user/index']];
}
$menuItems[] = ['label' => Html::icon("list") . "&nbsp;" . Yii::t("app", 'Motions'), 'url' => ['motiontag/index']];

if (Yii::$app->user->isGuest) {
	$menuItems[] = ['label' => Html::icon("pencil") . "&nbsp;" . Yii::t("app", 'Register'), 'url' => ['/site/signup']];
	$menuItems[] = ['label' => Html::icon("log-in") . "&nbsp;" . Yii::t("app", 'Login'), 'url' => ['/site/login']];
} else {
	$menuItems[] = [
		'label' => Html::icon("user") . "&nbsp;" . Yii::t("app", "{user}'s Profile", ["user" => Yii::$app->user->getModel()->givenname]),
		'url' => ['user/view', 'id' => Yii::$app->user->id],
	];
	$menuItems[] = [
		'label' => Html::icon("tags") . "&nbsp;" . Yii::t("app", "{user}'s History", ["user" => Yii::$app->user->getModel()->givenname]),
		'url' => ['history/index', 'user_id' => Yii::$app->user->id],
	];
	$menuItems[] = [
		'label' => Html::icon("log-out") . "&nbsp;" . Yii::t("app", 'Logout'),
		'url'   => ['/site/logout'],
		'linkOptions' => ['data-method' => 'post']
	];
}
echo Nav::widget([
	'options' => ['class' => 'navbar-nav menu navbar-right'],
	'items'   => $menuItems,
	'encodeLabels' => false,
]);
NavBar::end();