<?php

use yii\helpers\Html;
use yii\helpers\Url;

?>
<div class="row tournament">
	<a href="<?= Url::to(['view', 'id' => $model->id]) ?>">
		<div class="col-sm-2 col-md-2 col-md-push-1 text-center image">
			<?= $model->getLogoImage(150, 150) ?>
		</div>
		<div class="col-sm-5 col-sm-push-1 col-md-5 col-md-push-1 text-center name">
			<h2><?= Html::encode($model->name) ?></h2>
			<h4><?= Yii::$app->formatter->asDate($model->start_date, "short") ?>
				- <?= Yii::$app->formatter->asDate($model->end_date, "short") ?></h4>
		</div>
		<div class="col-sm-4 col-sm-push-1 col-md-4 col-md-push-1 hidden-xs details">
			<div class="col-sm-12">
				<?= Yii::t("app", "Hosted by") ?>:
				<?= Html::encode($model->hostedby->fullname) ?>
				(<?= strtoupper(Html::encode($model->hostedby->country->alpha_2)) ?>)
			</div>
			<div class="col-sm-12">
				<?= Yii::t("app", "Teams") ?>:  <?= Html::encode($model->getTeams()->count()) ?>
			</div>
			<div class="col-sm-12">
				<?= Yii::t("app", "Adjudicators") ?>:  <?= Html::encode($model->getAdjudicators()->count()) ?>
			</div>
		</div>
	</a>
</div>
