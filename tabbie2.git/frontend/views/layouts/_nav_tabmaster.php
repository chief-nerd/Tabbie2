<?
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use common\models\Tournament;
use kartik\helpers\Html;

$icon_class = ["class" => "hidden-md"];

NavBar::begin([
	'brandLabel' => Yii::t("app", "{tournament} - Tabmaster", ["tournament" => $tournament->name]),
	'brandUrl' => Yii::$app->urlManager->createUrl(["tournament/view", "id" => $tournament->id]),
	'options'  => [
		'class' => 'navbar navbar-default navbar-fixed-top navbar-sub',
	],
]);

$rounds = [];
$results = [];
foreach ($tournament->rounds as $r) {
	$rounds[] = ['label' => Yii::t("app", "Round #{number}", ["number" => $r->number]), 'url' => ['round/view', "id" => $r->id, "tournament_id" => $tournament->id]];
	$results[] = ['label' => Yii::t("app", "Result Round #{number}", ["number" => $r->number]), 'url' => ['result/round', "id" => $r->id, "tournament_id" => $tournament->id]];
}

$venue_items = [
	['label' => Html::icon("list") . "&nbsp;" . Yii::t("app", 'List Venues'), 'url' => ['venue/index', "tournament_id" => $tournament->id]],
	(($tournament->status < Tournament::STATUS_CLOSED) ? ['label' => Html::icon("plus") . "&nbsp;" . Yii::t("app", 'Create Venue'), 'url' => ['venue/create', "tournament_id" => $tournament->id]] : ""),
	(($tournament->status < Tournament::STATUS_CLOSED) ? ['label' => Html::icon("import") . "&nbsp;" . Yii::t("app", 'Import Venue'), 'url' => ['venue/import', "tournament_id" => $tournament->id]] : ""),
];

$team_items = [
	['label' => Html::icon("list") . "&nbsp;" . Yii::t("app", 'List Teams'), 'url' => ['team/index', "tournament_id" => $tournament->id]],
	(($tournament->status < Tournament::STATUS_CLOSED) ? ['label' => Html::icon("plus") . "&nbsp;" . Yii::t("app", 'Create Team'), 'url' => ['team/create', "tournament_id" => $tournament->id]] : ""),
	(($tournament->status < Tournament::STATUS_CLOSED) ? ['label' => Html::icon("import") . "&nbsp;" . Yii::t("app", 'Import Team'), 'url' => ['team/import', "tournament_id" => $tournament->id]] : ""),
	'<li class="divider"></li>',
	['label' => Html::icon("flag") . "&nbsp;" . Yii::t("app", 'Strike Team'), 'url' => ['strike/team_index', "tournament_id" => $tournament->id]],
];

if ($tournament->has_esl) {
	$team_items[] = '<li class="divider"></li>';
	$team_items[] = ['label' => Yii::t("app", 'Language Officers'), 'url' => ['language/officer', "tournament_id" => $tournament->id]];
	$team_items[] = ['label' => Yii::t("app", 'Language Status Review'), 'url' => ['language/index', "tournament_id" => $tournament->id]];
}

$adjudicator_items = [
	['label' => Html::icon("list") . "&nbsp;" . Yii::t("app", 'List Adjudicators'), 'url' => ['adjudicator/index', "tournament_id" => $tournament->id]],
	(($tournament->status < Tournament::STATUS_CLOSED) ? ['label' => Html::icon("plus") . "&nbsp;" . Yii::t("app", 'Create Adjudicator'), 'url' => ['adjudicator/create', "tournament_id" => $tournament->id]] : ""),
	(($tournament->status < Tournament::STATUS_CLOSED) ? ['label' => Html::icon("import") . "&nbsp;" . Yii::t("app", 'Import Adjudicator'), 'url' => ['adjudicator/import', "tournament_id" => $tournament->id]] : ""),
	'<li class="divider"></li>',
	['label' => Html::icon("list") . "&nbsp;" . Yii::t("app", 'View Preset Panels'), 'url' => ["panel/index", "tournament_id" => $tournament->id]],
	(($tournament->status < Tournament::STATUS_CLOSED) ? ['label' => Html::icon("plus") . "&nbsp;" . Yii::t("app", 'Create Preset Panel'), 'url' => ["panel/create", "tournament_id" => $tournament->id]] : ""),
	'<li class="divider"></li>',
	['label' => Html::icon("flag") . "&nbsp;" . Yii::t("app", 'Strike Adjudicator'), 'url' => ['strike/adjudicator_index', "tournament_id" => $tournament->id]],
];

$tournament_items = [
	(($tournament->status < Tournament::STATUS_CLOSED) ? ['label' => Yii::t("app", 'Update Tournament'), 'url' => ['tournament/update', "id" => $tournament->id]] : ""),
	(($tournament->status < Tournament::STATUS_CLOSED) ? '<li class="divider"></li>' : ""),
	['label' => Html::icon("list") . "&nbsp;" . Yii::t("app", 'Display Team Tab'), 'url' => ['tab/live-team', "tournament_id" => $tournament->id]],
	['label' => Html::icon("list") . "&nbsp;" . Yii::t("app", 'Display Speaker Tab'), 'url' => ['tab/live-speaker', "tournament_id" => $tournament->id]],
	'<li class="divider"></li>',
	(($tournament->status < Tournament::STATUS_CLOSED) ? ['label'       => Html::icon("export") . "&nbsp;" . Yii::t("app", 'Publish Tab'), 'url' => ['tab/publish', "tournament_id" => $tournament->id],
														  'linkOptions' => ['data' => [
															  "confirm" => Yii::t("app", "Publishing the Tab will close and archive the tournament!! Are you sure you want to continue?")
														  ]]] : ""),
	(($tournament->status < Tournament::STATUS_CLOSED) ? '<li class="divider"></li>' : ""),
	(($tournament->status < Tournament::STATUS_CLOSED) ? ['label' => Html::icon("list") . "&nbsp;" . Yii::t("app", 'Missing Users'), 'url' => ['public/missing-user', "tournament_id" => $tournament->id, "accessToken" => $tournament->accessToken], 'linkOptions' => ['target' => '_blank']] : ""),
	(($tournament->status < Tournament::STATUS_CLOSED) ? ['label' => Html::icon("modal-window") . "&nbsp;" . Yii::t("app", 'Checkin Form'), 'url' => ['checkin/input', "tournament_id" => $tournament->id, "accessToken" => $tournament->accessToken]] : ""),
	(($tournament->status < Tournament::STATUS_CLOSED) ? ['label' => Html::icon("barcode") . "&nbsp;" . Yii::t("app", 'Print Barcodes'), 'url' => ['checkin/generate-barcodes', "tournament_id" => $tournament->id, "accessToken" => $tournament->accessToken]] : ""),
	(($tournament->status < Tournament::STATUS_CLOSED) ? ['label' => Html::icon("barcode") . "&nbsp;" . Yii::t("app", 'Print Badgets'), 'url' => ['checkin/generate-badges', "tournament_id" => $tournament->id, "accessToken" => $tournament->accessToken]] : ""),
	(($tournament->status < Tournament::STATUS_CLOSED) ? ['label'       => Html::icon("erase") . "&nbsp;" . Yii::t("app", 'Reset Checkin'), 'url' => ['checkin/reset', "tournament_id" => $tournament->id],
														  'linkOptions' => ['data' => [
															  "confirm" => Yii::t("app", "Are you sure you want to reset the checkin?")
														  ]]] : ""),
	(($tournament->status < Tournament::STATUS_CLOSED) ? '<li class="divider"></li>' : ""),
	(($tournament->status < Tournament::STATUS_CLOSED) ? ['label' => Html::icon("transfer") . "&nbsp;" . Yii::t('app', 'Sync with DebReg'), 'url' => ["tournament/debreg-sync", "id" => $tournament->id]] : ""),
	(($tournament->status < Tournament::STATUS_CLOSED) ? ['label'       => Html::icon("export") . "&nbsp;" . Yii::t('app', 'Migrate to Tabbie 1'), 'url' => ["tournament/migrate-tabbie", "id" => $tournament->id],
														  'linkOptions' => ['data' => [
															  "confirm" => Yii::t("app", "Extreme caution young padawan!")
														  ]]] : ""),
];


$menuItems = [
	['label' => Html::icon("tower", $icon_class) . "&nbsp;" . 'Venues', 'url' => '#',
	 "items" => $venue_items,
	],
	['label' => Html::icon("bullhorn", $icon_class) . "&nbsp;" . Yii::t("app", 'Teams'), 'url' => '#',
	 "items" => $team_items,
	],
	['label' => Html::icon("dashboard", $icon_class) . "&nbsp;" . Yii::t("app", 'Adjudicators'), 'url' => '#',
	 "items" => $adjudicator_items,
	],
	['label' => Html::icon("th-list", $icon_class) . "&nbsp;" . Yii::t("app", 'Rounds'), 'url' => '#',
	 "items" => array_merge_recursive([
		 ['label' => Html::icon("list") . "&nbsp;" . Yii::t("app", 'List Rounds'), 'url' => ['round/index', "tournament_id" => $tournament->id]],
		 (($tournament->status < Tournament::STATUS_CLOSED) ? ['label' => Html::icon("plus") . "&nbsp;" . Yii::t("app", 'Create Round'), 'url' => ['round/create', "tournament_id" => $tournament->id]] : ""),
		 '<li class="divider"></li>',
		 ['label' => Yii::t("app", 'Energy Options'), 'url' => ['energy/index', "tournament_id" => $tournament->id]],
		 '<li class="divider"></li>',
	 ], $rounds),
	],
	['label' => Html::icon("envelope", $icon_class) . "&nbsp;" . Yii::t("app", 'Results'), 'url' => '#',
	 "items" => array_merge_recursive([
		 ['label' => Html::icon("list") . "&nbsp;" . Yii::t("app", 'List Results'), 'url' => ['result/index', "tournament_id" => $tournament->id]],
		 ['label' => Html::icon("plus") . "&nbsp;" . Yii::t("app", 'Insert Ballot'), 'url' => ['result/manual', "tournament_id" => $tournament->id]],
		 '<li class="divider"></li>',
		 ['label' => Yii::t("app", 'Correct Cache'), 'url' => ['result/correctcache', "tournament_id" => $tournament->id]],
		 '<li class="divider"></li>',
	 ], $results),
	],
	['label' => Html::icon("comment", $icon_class) . "&nbsp;" . Yii::t("app", 'Feedback'), 'url' => '#',
	 "items" => [
		 ['label' => Yii::t("app", 'Setup Questions'), 'url' => ['question/index', "tournament_id" => $tournament->id]],
		 '<li class="divider"></li>',
		 ['label' => Yii::t("app", 'Every Feedback'), 'url' => ['feedback/index', "tournament_id" => $tournament->id]],
		 ['label' => Yii::t("app", 'Feedback on Adjudicator'), 'url' => ['feedback/adjudicator', "tournament_id" => $tournament->id]],
		 //['label' => Yii::t("app", 'Feedback in Room'), 'url' => ['feedback/room', "tournament_id" => $tournament->id]],
		 //'<li class="divider"></li>',
		 //['label' => Yii::t("app", 'Tournament Feedback'), 'url' => ['feedback/tournament', "tournament_id" => $tournament->id]],
		 //['label' => Yii::t("app", '{app} System Feedback', ["app" => Yii::$app->params["appName"]]), 'url' => ['feedback/tabbie', "tournament_id" => $tournament->id]],
	 ]
	],
	['label' => Html::icon("stats", $icon_class) . "&nbsp;" . Yii::t("app", 'Tournament'), 'url' => "#",
	 "items" => $tournament_items,
	],
];
$navbar = Nav::widget(['options'      => [
	'class' => 'navbar-nav navbar-right'],
					   'items'        => $menuItems,
					   'encodeLabels' => false,
]);
echo $navbar;
NavBar::end();