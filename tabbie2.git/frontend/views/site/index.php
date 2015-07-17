<?php

	use kartik\helpers\Html;

	$fb_Banner = "https://s3.eu-central-1.amazonaws.com/tabbie-assets/FB_banner.jpg";
	$fb_Logo = "https://s3.eu-central-1.amazonaws.com/tabbie-assets/FB_logo.jpg";

	$this->registerMetaTag(["property" => "og:title", "content" => Yii::$app->params["appName"] . " - " . Yii::$app->params["slogan"]], "og:title");
	$this->registerMetaTag(["property" => "og:image", "content" => $fb_Logo], "og:image1");
	$this->registerMetaTag(["property" => "og:image", "content" => $fb_Banner], "og:image2");
	$this->registerLinkTag(["rel" => "apple-touch-icon", "href" => $fb_Logo], "apple-touch-icon");


	/* @var $this yii\web\View */
	$this->title = Yii::$app->params["slogan"];
?>
<div class="site-index">
	<div class="beta"></div>
	<div class="jumbotron">
		<? if (count($tournaments) > 0): ?>
			<h4><?= Yii::t("app", "Current BP Debate {count, plural, =0{Tournament} =1{Tournament} other{Tournaments}}", ["count" => count($tournaments)]) ?>
				:</h4>

			<div class="tournaments row">
				<?
					$amount = count($tournaments);
					$full_cols = "col-xs-12 col-sm-6 col-md-4 col-lg-3";
					$posCorrect = "";
					$fix = false;

					foreach ($tournaments as $index => $t):
						if ($t instanceof \common\models\Tournament):
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
						<? endif; ?>
					<? endforeach; ?>
			</div>
		<? else: ?>
			<h1><?= Yii::t("app", "Welcome to {appName}!", ["appName" => Yii::$app->params["appName"]]) ?></h1>
			<p class="lead"><?= Yii::$app->params["slogan"] ?></p>
		<? endif; ?>
		<br><br>

		<div class="row">
			<div class="col-xs-12 col-sm-6 col-md-5 col-lg-4 col-lg-offset-1">
				<?= Html::a(Html::icon("calendar") . "&nbsp;" . Yii::t("app", "View Tournaments"), ['tournament/index'], ["class" => "btn btn-lg btn-primary btn-block"]) ?>
			</div>
			<div class="col-xs-12 hidden-sm col-md-2 col-lg-2">
				&nbsp;
			</div>
			<div class="col-xs-12 col-sm-6 col-md-5 col-lg-4">
				<?= Html::a(Html::icon("plus") . "&nbsp;" . Yii::t("app", "Create Tournament"), ['tournament/create'], ["class" => "btn btn-lg btn-success btn-block"]) ?>
			</div>
		</div>
	</div>

	<div class="body-content">

		<div class="row">
			<div class="col-lg-4">
				<h2>Modern BP Tabbing Solution</h2>

				<p>Tabbie2 is Amazon AWS hosted and comes with a mobile responsive frontend, e-Ballots and e-Feedback
					system.
					Therefor no setup required! Just register, import your
					data via CSV or DebReg link and start tabbing.</p>
			</div>
			<div class="col-lg-4">
				<h2>Community Project</h2>

				<p>The Tabbie2 source code will be made open source after Vienna EUDC to help programmers from the
					community to actively engage in the project.</p>
			</div>
			<div class="col-lg-4">
				<h2>Fair and Fast</h2>

				<p>Rock solid tabbing algorithm based on the famous Tabbie software. It even provides Gender Balancing
					and other cool features.</p>
			</div>
		</div>

	</div>
</div>
