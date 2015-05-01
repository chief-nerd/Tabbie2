<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
	<h1><?= Html::encode($this->title) ?></h1>

	<p>Tabbie 2 is the new edition of the famous Tabbie system. Lots of cool new features and awesome stuff! Go check it
		out.<br>
		Yea I mean seriously, what else should I write here ... if you know something send me an email to
		jakob@tabbie.org...
	</p>

	<div class="row">
		<div class="col-sm-6">
			<h2>Import Data Masks</h2>
			<?
			$sampleDir = "@frontend/assets/csv/";
			$asset = Yii::$app->assetManager->publish($sampleDir)[1];
			?>
			<ul>
				<li><?= Html::a(Yii::t("app", "Venue CSV"), $asset . "/Import_Venue.csv") ?></li>
				<li><?= Html::a(Yii::t("app", "Adjudicator CSV"), $asset . "/Import_Adjudicator.csv") ?></li>
				<li><?= Html::a(Yii::t("app", "Team CSV"), $asset . "/Import_Team.csv") ?></li>
			</ul>
		</div>
		<div class="col-sm-6">
			<h2>Sample Data</h2>
			<?
			$sampleFile = Yii::getAlias($sampleDir) . "/Sample_Venues.csv";
			if (file_exists($sampleFile)) {
				$mtime = filemtime($sampleFile);
				?>
				<small><?= Yii::t("app", "created") ?> <?= Yii::$app->formatter->asDatetime($mtime) ?></small>
				<br><br>
				<ul>
					<li><?= Html::a(Yii::t("app", "Sample Venue CSV"), $asset . "/Sample_Venues.csv") ?></li>
					<li><?= Html::a(Yii::t("app", "Sample Team CSV"), $asset . "/Sample_Teams.csv") ?></li>
					<li><?= Html::a(Yii::t("app", "Sample Adjudicator CSV"), $asset . "/Sample_Adjudicator.csv") ?></li>
				</ul>
			<?
			}
			else
				echo "not yet created";
			?>
		</div>
	</div>
</div>
