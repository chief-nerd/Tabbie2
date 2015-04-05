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
	<?
	echo $this->render("_nav");

	/* @var $tournament common\models\Tournament */
	if ($this->context->hasMethod("_getContext")) {
		$tournament = $this->context->_getContext();
		if ($tournament instanceof \common\models\Tournament) {

			if (Yii::$app->user->isTabMaster($tournament))
				echo $this->render("_nav_tabmaster", ["tournament" => $tournament]);
			else if (Yii::$app->user->isLanguageOfficer($tournament))
				echo $this->render("_nav_languageofficer", ["tournament" => $tournament]);

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
	<?= $this->render("_loader") ?>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
