<?
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;

NavBar::begin([
	'brandLabel' => $tournament->name . " - Tabmaster",
	'brandUrl' => Yii::$app->urlManager->createUrl(["tournament/view", "id" => $tournament->id]),
	'options' => [
		'class' => 'navbar navbar-default navbar-fixed-top navbar-sub',
	],
]);

$rounds = array();
$results = array();
foreach ($tournament->rounds as $r) {
	$rounds[] = ['label' => "Round #$r->number", 'url' => ['round/view', "id" => $r->id, "tournament_id" => $tournament->id]];
	$results[] = ['label' => "Result Round #$r->number", 'url' => ['result/round', "id" => $r->id, "tournament_id" => $tournament->id]];
}

$team_items = [
	['label' => 'List Teams', 'url' => ['team/index', "tournament_id" => $tournament->id]],
	['label' => 'Create Team', 'url' => ['team/create', "tournament_id" => $tournament->id]],
	['label' => 'Import Team', 'url' => ['team/import', "tournament_id" => $tournament->id]],
	'<li class="divider"></li>',
	['label' => 'Strike Team', 'url' => ['strike/team_index', "tournament_id" => $tournament->id]],
];

if ($tournament->has_esl) {
	$team_items[] = '<li class="divider"></li>';
	$team_items[] = ['label' => 'Language Officers', 'url' => ['language/officer', "tournament_id" => $tournament->id]];
	$team_items[] = ['label' => 'Language Status Review', 'url' => ['language/index', "tournament_id" => $tournament->id]];
}

$menuItems = [
	['label' => 'Venues', 'url' => '#',
		"items" => [
			['label' => 'List Venues', 'url' => ['venue/index', "tournament_id" => $tournament->id]],
			['label' => 'Create Venue', 'url' => ['venue/create', "tournament_id" => $tournament->id]],
			['label' => 'Import Venue', 'url' => ['venue/import', "tournament_id" => $tournament->id]],
		]
	],
	['label' => 'Teams', 'url' => '#',
		"items" => $team_items,
	],
	['label' => 'Adjudicators', 'url' => '#',
		"items" => [
			['label' => 'List Adjudicators', 'url' => ['adjudicator/index', "tournament_id" => $tournament->id]],
			['label' => 'Create Adjudicator', 'url' => ['adjudicator/create', "tournament_id" => $tournament->id]],
			['label' => 'Import Adjudicator', 'url' => ['adjudicator/import', "tournament_id" => $tournament->id]],
			'<li class="divider"></li>',
			['label' => 'Preset Adj. Panels', 'url' => ["panel/create", "tournament_id" => $tournament->id]],
			'<li class="divider"></li>',
			['label' => 'Strike Adjudicator', 'url' => ['strike/adjudicator_index', "tournament_id" => $tournament->id]],
		]
	],
	['label' => 'Rounds', 'url' => '#',
		"items" => array_merge_recursive([
			['label' => 'List Rounds', 'url' => ['round/index', "tournament_id" => $tournament->id]],
			['label' => 'Create Round', 'url' => ['round/create', "tournament_id" => $tournament->id]],
			'<li class="divider"></li>',
			['label' => 'Energy Options', 'url' => ['energy/index', "tournament_id" => $tournament->id]],
			'<li class="divider"></li>',
		], $rounds),
	],
	['label' => 'Results', 'url' => '#',
		"items" => array_merge_recursive([
			['label' => 'List Results', 'url' => ['result/index', "tournament_id" => $tournament->id]],
			['label' => 'Correct Cache', 'url' => ['result/correctcache', "tournament_id" => $tournament->id]],
			'<li class="divider"></li>',
		], $results),
	],
	['label' => 'Current Tab', 'url' => "#",
		"items" => [
			['label' => 'Team Tab', 'url' => ['tab/live-team', "tournament_id" => $tournament->id]],
			['label' => 'Speaker Tab', 'url' => ['tab/live-speaker', "tournament_id" => $tournament->id]],
			'<li class="divider"></li>',
			['label' => 'Publish Tab', 'url' => ['tab/publish', "tournament_id" => $tournament->id],
				'linkOptions' => ['data' => [
					"confirm" => "Publishing the Tab will close and archive the tournament!! Are you sure you want to continue?"
				]]],
			'<li class="divider"></li>',
			['label' => 'Missing User', 'url' => ['display/missinguser', "tournament_id" => $tournament->id], 'linkOptions' => ['target' => '_blank']],
			['label' => 'Checkin Form', 'url' => ['tournament/checkin', "id" => $tournament->id]],
			['label' => 'Reset Checkin', 'url' => ['tournament/checkinreset', "id" => $tournament->id],
				'linkOptions' => ['data' => [
					"confirm" => "Are you sure you want to reset the checkin?"
				]]],
		]
	],
	['label' => 'Feedback', 'url' => '#',
		"items" => [
			['label' => 'Every Feedback', 'url' => ['feedback/index', "tournament_id" => $tournament->id]],
			'<li class="divider"></li>',
			['label' => 'Adjudicator Feedback', 'url' => ['feedback/adjudicator', "tournament_id" => $tournament->id]],
			['label' => 'Team to Chair Feedback', 'url' => ['feedback/team', "tournament_id" => $tournament->id]],
			'<li class="divider"></li>',
			['label' => 'Setup Questions', 'url' => ['question/index', "tournament_id" => $tournament->id]],
			'<li class="divider"></li>',
			['label' => 'Tournament Feedback', 'url' => ['feedback/tournament', "tournament_id" => $tournament->id]],
			['label' => Yii::$app->params["appName"] . ' Feedback', 'url' => ['feedback/tabbie', "tournament_id" => $tournament->id]],
		]
	],
];

echo Nav::widget(['options' => ['class' => 'navbar-nav navbar-right'], 'items' => $menuItems,]);
NavBar::end();