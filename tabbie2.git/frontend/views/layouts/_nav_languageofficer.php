<?
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;

NavBar::begin([
	'brandLabel' => $tournament->name . " - Language Officer",
	'brandUrl' => Yii::$app->urlManager->createUrl(["tournament/view", "id" => $tournament->id]),
	'options' => [
		'class' => 'navbar navbar-default navbar-fixed-top navbar-sub',
	],
]);

$menuItems = [
	['label' => 'Review Language Status', 'url' => ['tournament/language', "id" => $tournament->id]],
];

echo Nav::widget(['options' => ['class' => 'navbar-nav navbar-right'], 'items' => $menuItems,]);
NavBar::end();