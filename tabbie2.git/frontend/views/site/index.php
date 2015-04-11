<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = Yii::$app->params["slogan"];
?>
<div class="site-index">

	<div class="jumbotron">
		<h1><?= Yii::t("app", "Welcome to {appName}!", ["appName" => Yii::$app->params["appName"]]) ?></h1>

		<p class="lead"><?= Yii::$app->params["slogan"] ?></p>
		<h4><?= Yii::t("app", "Current {count, plural, =0{Tournament} =1{Tournament} other{Tournaments}}", ["count" => count($tournaments)]) ?>
			:</h4>

		<div class="tournaments row">
			<?
			$amount = count($tournaments);
			$full_cols = "col-xs-12 col-sm-6 col-md-4 col-lg-3";
			$posCorrect = "";
			$fix = false;

			foreach ($tournaments as $index => $t):
				?>
				<a href="<?= \yii\helpers\Url::to(["tournament/view", "id" => $t->id]) ?>">
					<?
					$left = $amount % 4;
					if (($amount - $index) >= $left + 1)
						$cols = $full_cols;
					else {
						if ($left == 3 && !$fix) {
							$cols = "col-xs-12 col-sm-6 col-md-4 col-lg-4";
							$fix = true;
						}
						if ($left == 2 && !$fix) {
							$cols = "col-xs-12 col-sm-6 col-md-6 col-lg-6";
							$fix = true;
						}
						if ($left == 1 && !$fix) {
							$cols = "col-xs-12 col-sm-12 col-md-12 col-lg-12";
							$fix = true;
						}
						if ($left == 0 && !$fix) {
							$cols = $full_cols;
							$fix = true;
						}
					}
					?>
					<div class="tournament <?= $cols ?> <?= $posCorrect ?>">
						<?= $t->getLogoImage(null, 100) ?>

						<h2><?= $t->name ?></h2>
					</div>
				</a>
			<? endforeach; ?>
		</div>
		<br><br>

		<p>
			<?= Html::a(Yii::t("app", "View all Tournaments"), ['tournament/index'], ["class" => "btn btn-lg btn-success"]) ?>
			&nbsp;&nbsp;or&nbsp;&nbsp;
			<?= Html::a(Yii::t("app", "Create new Tournament"), ['tournament/create'], ["class" => "btn btn-lg btn-primary"]) ?>
		</p>
	</div>

	<div class="body-content">

		<div class="row">
			<div class="col-lg-4">
				<h2>New</h2>

				<p>All new on a centraliesed webserver. <br>Lorem ipsum dolor sit amet, consetetur sadipscing elitr,
					sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.
					At vero eos et accusam et justo duo dolores et ea rebum.</p>

			</div>
			<div class="col-lg-4">
				<h2>Way</h2>

				<p>Viewer / App / Modules ... there are many ways to perfection<br>Lorem ipsum dolor sit amet,
					consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna
					aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.</p>
			</div>
			<div class="col-lg-4">
				<h2>Tabbing</h2>

				<p>Rock solid tabbing algorithm based on the famous Tabbie software<br>Lorem ipsum dolor sit amet,
					consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna
					aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.</p>
			</div>
		</div>

	</div>
</div>
