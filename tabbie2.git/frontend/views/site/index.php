<?php

use kartik\helpers\Html;

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
    <? if (!Yii::$app->user->isGuest && Yii::$app->user->identity->gdprconsent != 1): ?>
        <div class="row">
            <div class="col-xs-12 hidden-sm col-md-2 col-lg-2">
                &nbsp;
            </div>
        </div>
        <div class="row">
          <div class="col">
            <?= Html::a(\kartik\helpers\Html::icon("cog") . "&nbsp" . Yii::t('app', 'Update GDPR Settings'), ['/user/update', 'id' => Yii::$app->user->id], ['class' => 'btn btn-primary btn-danger']) ?>
          </div>
        </div>
    <? endif; ?>
	</div>

	<div class="body-content">

		<div class="row">
			<div class="col-lg-4">
				<h2>Modern BP Tabbing Solution</h2>

				<p>Tabbie2 is Amazon AWS hosted and comes with a mobile responsive frontend, e-Ballots and e-Feedback
					system.
					Therefore no setup is required! Just register, import your
					data via CSV or DebReg link and start tabbing.</p>
			</div>
			<div class="col-lg-4">
				<h2>Community Project</h2>

                <p>The <a href="https://github.com/JakobReiter/tabbie2-algorithms">Tabbie2 algorithm source code</a> is
                    open source to foster a transparent approach to tabbing. If you want to contribute to the project
                    then drop us a line at support@tabbie.org.</p>
			</div>
			<div class="col-lg-4">
				<h2>Fair and Fast</h2>

				<p>Rock solid tabbing algorithm based on the famous Tabbie software. It even provides Gender Balancing
					and other cool features.</p>
			</div>
		</div>

	</div>
</div>

<script type="application/ld+json">
<?
	$schema = [
		"@context"      => "http://schema.org",
		"@type"         => "NGO",
		"name"          => Yii::$app->params["appName"],
		"alternateName" => "TabbieTwo",
		"url"           => Yii::$app->params["appUrl"],
		"description"   => Yii::$app->params["slogan"],
		"email"         => Yii::$app->params["supportEmail"],
		"logo"          => "https://s3.eu-central-1.amazonaws.com/tabbie-assets/FB_logo.jpg",
		"location"      => "Vienna, Austria",
		"sameAs"        => [
			"https://www.facebook.com/TabbieTwo",
			"https://twitter.com/TabbieTwo",
		],
		"events"        => [],
	];

	/** @var common\models\Tournament $t */
	foreach ($upcoming as $t) {
		$schema["events"][] = $t->getSchema(false);
	}

	echo json_encode($schema, JSON_UNESCAPED_SLASHES);
	?>

</script>
