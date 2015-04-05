<?php

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use frontend\widgets\Alert;
use common\models\Tournament;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<?
if ($this->context->hasMethod("_getContext")) {
	$tournament = $this->context->_getContext();
	if ($tournament instanceof Tournament && (Yii::$app->user->isTabMaster($tournament) || Yii::$app->user->isAdmin())) {
		$addclass = "movedown";
	}
}
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
	<meta charset="<?= Yii::$app->charset ?>"/>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<?
	if (isset($tournament) && $tournament instanceof Tournament) {
		echo '<link rel = "apple-touch-icon" href = "' . $tournament->logo . '"/>';
	}
	?>

	<?= Html::csrfMetaTags() ?>
	<title><?= Html::encode($this->title) ?> :: <?= Html::encode(Yii::$app->params["appName"]) ?></title>
	<?php $this->head() ?>
</head>
<body class="<?= isset($addclass) ? $addclass : "" ?>">
<?php $this->beginBody() ?>
<div class="flashes">
	<?= Alert::widget() ?>
</div>
<div class="wrap">
	<?php
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
	?>
	<?
	/* @var $tournament common\models\Tournament */
	if ($this->context->hasMethod("_getContext")) {
		$tournament = $this->context->_getContext();
		if ($tournament instanceof \common\models\Tournament && (Yii::$app->user->isTabMaster($tournament) || Yii::$app->user->isAdmin())) {

			NavBar::begin([
				'brandLabel' => $tournament->name . " Tabmaster",
				'brandUrl' => Yii::$app->urlManager->createUrl(["tournament/view", "id" => $tournament->id]),
				'options' => [
					'class' => 'navbar navbar-default navbar-fixed-top tabmaster',
				],
			]);

			$rounds = array();
			$results = array();
			foreach ($tournament->rounds as $r) {
				$rounds[] = ['label' => "Round #$r->number", 'url' => ['round/view', "id" => $r->id, "tournament_id" => $tournament->id]];
				$results[] = ['label' => "Result Round #$r->number", 'url' => ['result/round', "id" => $r->id, "tournament_id" => $tournament->id]];
			}
			$menuItems = [
				['label' => 'Venues', 'url' => '#',
					"items" => [
						['label' => 'List Venues', 'url' => ['venue/index', "tournament_id" => $tournament->id]],
						['label' => 'Create Venue', 'url' => ['venue/create', "tournament_id" => $tournament->id]],
					]
				],
				['label' => 'Teams', 'url' => '#',
					"items" => [
						['label' => 'List Teams', 'url' => ['team/index', "tournament_id" => $tournament->id]],
						['label' => 'Create Team', 'url' => ['team/create', "tournament_id" => $tournament->id]],
						['label' => 'Import Team', 'url' => ['team/import', "tournament_id" => $tournament->id]],
						'<li class="divider"></li>',
						['label' => 'Strike Team', 'url' => ['strike/team_index', "tournament_id" => $tournament->id]],
					]
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
						['label' => 'Team Tab', 'url' => ['tab/team', "tournament_id" => $tournament->id]],
						['label' => 'Speaker Tab', 'url' => ['tab/speaker', "tournament_id" => $tournament->id]],
						'<li class="divider"></li>',
						['label' => 'Missing User', 'url' => ['display/missinguser', "tournament_id" => $tournament->id]],
						['label' => 'Checkin Form', 'url' => ['tournament/checkin', "id" => $tournament->id]],
						['label' => 'Reset Checkin', 'url' => ['tournament/checkinreset', "id" => $tournament->id]],
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
		}
	}
	?>
	<div class="container">
		<?=
		Breadcrumbs::widget(['links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],])
		?>
		<?= $content ?>
	</div>

	<footer class="footer">
		<div class="container">
			<p class="pull-left"><?= Yii::$app->params["appName"] ?> &copy; <?= date('Y') ?></p>

			<p class="pull-right"><?= HTML::a("Contact", 'mailto:' . Yii::$app->params["adminEmail"]) ?></p>
		</div>
	</footer>
	<div id="loader">
		<div class="container">
			<svg class="machine" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 645 526">
				<defs/>
				<g>
					<path x="-173,694" y="-173,694" class="large-shadow"
					      d="M645 194v-21l-29-4c-1-10-3-19-6-28l25-14 -8-19 -28 7c-5-8-10-16-16-24L602 68l-15-15 -23 17c-7-6-15-11-24-16l7-28 -19-8 -14 25c-9-3-18-5-28-6L482 10h-21l-4 29c-10 1-19 3-28 6l-14-25 -19 8 7 28c-8 5-16 10-24 16l-23-17L341 68l17 23c-6 7-11 15-16 24l-28-7 -8 19 25 14c-3 9-5 18-6 28l-29 4v21l29 4c1 10 3 19 6 28l-25 14 8 19 28-7c5 8 10 16 16 24l-17 23 15 15 23-17c7 6 15 11 24 16l-7 28 19 8 14-25c9 3 18 5 28 6l4 29h21l4-29c10-1 19-3 28-6l14 25 19-8 -7-28c8-5 16-10 24-16l23 17 15-15 -17-23c6-7 11-15 16-24l28 7 8-19 -25-14c3-9 5-18 6-28L645 194zM471 294c-61 0-110-49-110-110S411 74 471 74s110 49 110 110S532 294 471 294z"/>
				</g>
				<g>
					<path x="-136,996" y="-136,996" class="medium-shadow"
					      d="M402 400v-21l-28-4c-1-10-4-19-7-28l23-17 -11-18L352 323c-6-8-13-14-20-20l11-26 -18-11 -17 23c-9-4-18-6-28-7l-4-28h-21l-4 28c-10 1-19 4-28 7l-17-23 -18 11 11 26c-8 6-14 13-20 20l-26-11 -11 18 23 17c-4 9-6 18-7 28l-28 4v21l28 4c1 10 4 19 7 28l-23 17 11 18 26-11c6 8 13 14 20 20l-11 26 18 11 17-23c9 4 18 6 28 7l4 28h21l4-28c10-1 19-4 28-7l17 23 18-11 -11-26c8-6 14-13 20-20l26 11 11-18 -23-17c4-9 6-18 7-28L402 400zM265 463c-41 0-74-33-74-74 0-41 33-74 74-74 41 0 74 33 74 74C338 430 305 463 265 463z"/>
				</g>
				<g>
					<path x="-100,136" y="-100,136" class="small-shadow"
					      d="M210 246v-21l-29-4c-2-10-6-18-11-26l18-23 -15-15 -23 18c-8-5-17-9-26-11l-4-29H100l-4 29c-10 2-18 6-26 11l-23-18 -15 15 18 23c-5 8-9 17-11 26L10 225v21l29 4c2 10 6 18 11 26l-18 23 15 15 23-18c8 5 17 9 26 11l4 29h21l4-29c10-2 18-6 26-11l23 18 15-15 -18-23c5-8 9-17 11-26L210 246zM110 272c-20 0-37-17-37-37s17-37 37-37c20 0 37 17 37 37S131 272 110 272z"/>
				</g>
				<g>
					<path x="-100,136" y="-100,136" class="small"
					      d="M200 236v-21l-29-4c-2-10-6-18-11-26l18-23 -15-15 -23 18c-8-5-17-9-26-11l-4-29H90l-4 29c-10 2-18 6-26 11l-23-18 -15 15 18 23c-5 8-9 17-11 26L0 215v21l29 4c2 10 6 18 11 26l-18 23 15 15 23-18c8 5 17 9 26 11l4 29h21l4-29c10-2 18-6 26-11l23 18 15-15 -18-23c5-8 9-17 11-26L200 236zM100 262c-20 0-37-17-37-37s17-37 37-37c20 0 37 17 37 37S121 262 100 262z"/>
				</g>
				<g>
					<path x="-173,694" y="-173,694" class="large"
					      d="M635 184v-21l-29-4c-1-10-3-19-6-28l25-14 -8-19 -28 7c-5-8-10-16-16-24L592 58l-15-15 -23 17c-7-6-15-11-24-16l7-28 -19-8 -14 25c-9-3-18-5-28-6L472 0h-21l-4 29c-10 1-19 3-28 6L405 9l-19 8 7 28c-8 5-16 10-24 16l-23-17L331 58l17 23c-6 7-11 15-16 24l-28-7 -8 19 25 14c-3 9-5 18-6 28l-29 4v21l29 4c1 10 3 19 6 28l-25 14 8 19 28-7c5 8 10 16 16 24l-17 23 15 15 23-17c7 6 15 11 24 16l-7 28 19 8 14-25c9 3 18 5 28 6l4 29h21l4-29c10-1 19-3 28-6l14 25 19-8 -7-28c8-5 16-10 24-16l23 17 15-15 -17-23c6-7 11-15 16-24l28 7 8-19 -25-14c3-9 5-18 6-28L635 184zM461 284c-61 0-110-49-110-110S401 64 461 64s110 49 110 110S522 284 461 284z"/>
				</g>
				<g>
					<path x="-136,996" y="-136,996" class="medium"
					      d="M392 390v-21l-28-4c-1-10-4-19-7-28l23-17 -11-18L342 313c-6-8-13-14-20-20l11-26 -18-11 -17 23c-9-4-18-6-28-7l-4-28h-21l-4 28c-10 1-19 4-28 7l-17-23 -18 11 11 26c-8 6-14 13-20 20l-26-11 -11 18 23 17c-4 9-6 18-7 28l-28 4v21l28 4c1 10 4 19 7 28l-23 17 11 18 26-11c6 8 13 14 20 20l-11 26 18 11 17-23c9 4 18 6 28 7l4 28h21l4-28c10-1 19-4 28-7l17 23 18-11 -11-26c8-6 14-13 20-20l26 11 11-18 -23-17c4-9 6-18 7-28L392 390zM255 453c-41 0-74-33-74-74 0-41 33-74 74-74 41 0 74 33 74 74C328 420 295 453 255 453z"/>
				</g>
			</svg>
		</div>
	</div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
