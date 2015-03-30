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
			'label' => Yii::$app->user->getModel()->name . "'s Profile",
			'url' => ['user/view', 'id' => Yii::$app->user->id],
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
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
