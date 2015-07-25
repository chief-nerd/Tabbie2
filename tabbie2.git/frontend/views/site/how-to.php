<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'How-To';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-how-to">
	<h1><?= Html::encode($this->title) ?></h1>

	<div class="row">
		<div class="col-xs-12">
			<?= Html::ol([
				"1"  => "To create a new Tournament click the " . Html::a("Create Tournament", ["tournament/create"]) . " button and fill out the form.",
				"2"  => "Export your userdate into CSV file",
				"3"  => "Import every file into it's section at the platform",
				"4"  => "Check your data so that everything is loaded",
				"5"  => "Create a new Round",
				"6"  => "Publich the round",
				"7"  => "Take a beer and wait for all eBallots to be entered",
				"8"  => "Start a new round",
				"9"  => "... repeat",
				"10" => "Publish the tab form the Tournament Menu"
			], ["encode" => false]) ?>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<h2>CSV Import Data Masks</h2>
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
					<li><?= Html::a(Yii::t("app", "Sample Adjudicator CSV"), $asset . "/Sample_Adjudicators.csv") ?></li>
				</ul>
				<?
			} else
				echo "not yet created";
			?>
		</div>
	</div>
</div>
