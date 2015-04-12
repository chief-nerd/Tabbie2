<?php

use kartik\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Result */
/* @var $form yii\widgets\ActiveForm */
$this->title = Yii::t('app', 'Confirm Data for {venue}', ["venue" => $model->debate->venue->name]);
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="result-confirm">

	<?php $form = ActiveForm::begin(); ?>

	<?= Html::activeHiddenInput($model, 'debate_id') ?>
	<?
	/* @var $debate Debate */
	$debate = $model->debate;
	$cols = "col-xs-12 col-sm-6";
	$fieldOption = [
		"template" => "{label} {input}\n{hint}\n{error}",
	];
	$textOption = ["size" => 1, "maxlength" => 1, "readonly" => "readonly"];
	?>

	<div class="row">
		<div class="<?= $cols ?>">
			<h3><?= Yii::t("app", "Opening Government") ?></h3>
			<?= Html::activeHiddenInput($model, 'og_A_speaks'); ?>
			<?= Html::activeHiddenInput($model, 'og_B_speaks'); ?>
			<?= $form->field($model, 'og_place', $fieldOption)->label($debate->og_team->name)->textInput($textOption) ?>
		</div>
		<div class="<?= $cols ?>">
			<h3><?= Yii::t("app", "Opening Opposition") ?></h3>
			<?= Html::activeHiddenInput($model, 'oo_A_speaks'); ?>
			<?= Html::activeHiddenInput($model, 'oo_B_speaks'); ?>
			<?= $form->field($model, 'oo_place', $fieldOption)->label($debate->oo_team->name)->textInput($textOption) ?>
		</div>
	</div>
	<div class="row">
		<div class="<?= $cols ?>">
			<h3><?= Yii::t("app", "Closing Government") ?></h3>
			<?= Html::activeHiddenInput($model, 'cg_A_speaks'); ?>
			<?= Html::activeHiddenInput($model, 'cg_B_speaks'); ?>
			<?= $form->field($model, 'cg_place', $fieldOption)->label($debate->cg_team->name)->textInput($textOption) ?>
		</div>
		<div class="<?= $cols ?>">
			<h3><?= Yii::t("app", "Closing Opposition") ?></h3>
			<?= Html::activeHiddenInput($model, 'co_A_speaks'); ?>
			<?= Html::activeHiddenInput($model, 'co_B_speaks'); ?>
			<?= $form->field($model, 'co_place', $fieldOption)->label($debate->co_team->name)->textInput($textOption) ?>
		</div>
	</div>

	<?= Html::activeHiddenInput($model, "confirmed", ["value" => "true"]); ?>
	<hr>

	<div class="row">
		<div class="col-xs-5">
			<?= Html::a(Html::icon("trash") . "&nbsp;" . Yii::t('app', 'start over'), ["create", "id" => $debate->id, "tournament_id" => $debate->tournament_id], ['class' => 'btn btn-default btn-block']) ?>
		</div>
		<div class="col-xs-7">
			<?= Html::submitButton(Yii::t('app', 'Make it so!') . "&nbsp;" . Html::icon("send"), ['class' => 'btn btn-success btn-block']) ?>
		</div>
	</div>

	<?php ActiveForm::end(); ?>

</div>
