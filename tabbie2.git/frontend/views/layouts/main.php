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

$assetBundle = AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<?
if ($this->context->hasMethod("_getContext")) {
	$tournament = $this->context->_getContext();
	if ($tournament instanceof Tournament && ($tournament->isTabMaster(Yii::$app->user->id) || $tournament->isLanguageOfficer(Yii::$app->user->id))) {
		$addclass = "movedown";
	}
}
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
	<meta charset="<?= Yii::$app->charset ?>"/>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<?= Html::csrfMetaTags() ?>
	<title><?= Html::encode($this->title) ?> :: <?= Html::encode(Yii::$app->params["appName"]) ?></title>
	<?php $this->head() ?>
	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">

</head>
<body class="<?= isset($addclass) ? $addclass : "" ?>">
<?php $this->beginBody() ?>

<div class="flashes">
	<?= Alert::widget() ?>
</div>

<div class="wrap">
	<?
	/* @var $tournament common\models\Tournament */
	if ($this->context->hasMethod("_getContext")) {
		$tournament = $this->context->_getContext();
		if ($tournament instanceof \common\models\Tournament) {

			if ($tournament->isTabMaster(Yii::$app->user->id))
				echo $this->render("_nav_tabmaster", ["tournament" => $tournament]);
			else if ($tournament->isLanguageOfficer(Yii::$app->user->id))
				echo $this->render("_nav_languageofficer", ["tournament" => $tournament]);

		}
	}
	//Render later for overlapping
	echo $this->render("_nav");
	?>
	<div class="container">
		<div class="breadcrumbs hidden-xs">
			<?=
			Breadcrumbs::widget(['links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],])
			?>
		</div>
		<?= $content ?>
	</div>

	<footer class="footer">
		<div class="container">
			<p class="pull-left"><?= Yii::$app->params["appName"] ?> &copy; <?= date('Y') ?></p>

			<p class="pull-right">
				<?= HTML::a(Yii::t("app", "Report a Bug"), 'mailto:' . Yii::$app->params["supportEmail"], ["target" => "_blank"]) ?>
				<?= " | " ?>
				<?= HTML::a(Yii::t("app", "Contact"), 'mailto:' . Yii::$app->params["adminEmail"]) ?>
			</p>
		</div>
	</footer>
	<?= $this->render("_loader") ?>
</div>

<?php $this->endBody() ?>
<? if (YII_ENV == "prod") echo $this->render("_ga") ?>
</body>
</html>
<?php $this->endPage() ?>
