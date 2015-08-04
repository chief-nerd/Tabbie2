<?
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use kartik\helpers\Html;

NavBar::begin([
	'brandLabel' => Yii::t("app", "{tournament} - Language Officer", ["tournament" => $tournament->name]),
	'brandUrl' => Yii::$app->urlManager->createUrl(["tournament/view", "id" => $tournament->id]),
	'options'  => [
		'class' => 'navbar navbar-default navbar-fixed-top navbar-sub',
	],
]);

$menuItems = [
	['label' => Html::icon("list-alt") . "&nbsp;" . Yii::t("app", 'Review Language Status'), 'url' => ['language/index', "tournament_id" => $tournament->id]],
];

echo Nav::widget(['options' => ['class' => 'navbar-nav navbar-right'], 'items' => $menuItems, 'encodeLabels' => false]);
NavBar::end();