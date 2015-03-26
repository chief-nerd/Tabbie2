<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Result */
/* @var $form yii\widgets\ActiveForm */
$this->title = "Thank you";
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = "Result";
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="result-thankyou">
	<div class="row">
		<div class="col-sm-12">
			<center>
				<h1><?= Yii::t("app", "Thank you!") ?></h1>

				<h2 class="text-success"><?= Yii::t("app", "Results successfully saved") ?></h2>
				<?
				if ($place > 0) {
					$class = "";
					$pre = "";
					if ($place <= ($max / 4)) {
						$class = "success";
						$pre = '<i class="glyphicon glyphicon-star"></i> ' . Yii::t("app", "Speeeed Bonus!");
					}
					if ($place >= floor(($max / 4 * 3))) {
						$class = "danger";
						$pre = '<i class="glyphicon glyphicon-alert"></i> ' . Yii::t("app", "Hurry up! Chop Chop!");
					}
					if ($place == $max) {
						$class = "danger";
						$pre = '<i class="glyphicon glyphicon-alert"></i> ' . Yii::t("app", "Bummer! Last one!");
					}
					echo '<h3 class="text-' . $class . '">' . $pre . ' You are <b>#' . $place . '</b> from ' . $max . '</h3>';
				}
				?>
			</center>
		</div>
	</div>

	<hr>

	<div class="row">
		<div class="col-xs-6">
			<?= Html::a("Go to Home", ["tournament/view", "id" => $tournament->id], ["class" => "btn btn-default center-block"]) ?>
		</div>
		<div class="col-xs-6">
			<?= Html::a("Enter Feedback", ["feedback/create", "id" => $model->debate->id, "tournament_id" => $tournament->id], ["class" => "btn btn-success center-block"]) ?>
		</div>
	</div>
</div>