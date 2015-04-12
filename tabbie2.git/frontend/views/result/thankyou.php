<?php

use kartik\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Result */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t("app", "Thank you");
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = Yii::t("app", "Result");
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
						$pre = Html::icon("star") . "&nbsp;" . Yii::t("app", "Speeeed Bonus!");
					}
					if ($place >= floor(($max / 4 * 3))) {
						$class = "danger";
						$pre = Html::icon("exclamation-sign") . "&nbsp;" . Yii::t("app", "Hurry up! Chop Chop!");
					}
					if ($place == $max) {
						$class = "danger";
						$pre = Html::icon("alert") . "&nbsp;" . Yii::t("app", "Bummer! Last one!");
					}
					echo '<h3 class="text-' . $class . '">' . $pre . " " . Yii::t("app", "You are <b>#{place}</b> from {max}", [
							"place" => $place,
							"max" => $max,
						]) . '</h3>';
				}
				?>
			</center>
		</div>
	</div>

	<hr>

	<div class="row">
		<? /** @var $tournament Tournament */
		if ($tournament->getTournamentHasQuestions()->count() > 0): ?>
			<div class="col-xs-5">
				<?= Html::a(Html::icon("home") . "&nbsp;" . Yii::t("app", "Go Home"), ["tournament/view", "id" => $tournament->id], ["class" => "btn btn-default center-block"]) ?>
		</div>
			<div class="col-xs-7">
			<?
			//Can only be chair
			$ref = $model->debate->getChair()->id;

			?>
			<?= Html::a(Html::icon("comment") . "&nbsp;" . Yii::t("app", "Enter Feedback"), [
				"feedback/create",
				"id" => $model->debate->id,
				"type" => \common\models\Feedback::FROM_CHAIR,
				"ref" => $ref,
				"tournament_id" => $tournament->id],
				["class" => "btn btn-success center-block"]) ?>
		</div>
		<? else: ?>
			<div class="col-xs-12">
				<?= Html::a(Html::icon("home") . "&nbsp;" . Yii::t("app", "Go Home"), ["tournament/view", "id" => $tournament->id], ["class" => "btn btn-success center-block"]) ?>
			</div>
		<? endif; ?>
	</div>
</div>