<?php

use kartik\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Team;

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
		<? foreach (\common\models\Team::getPos() as $posindex => $pos): ?>
			<div class="<?= $cols ?>">
				<h3><?= Team::getPosLabel($posindex) ?></h3>
				<?= Html::activeHiddenInput($model, $pos . '_A_speaks'); ?>
				<?= Html::activeHiddenInput($model, $pos . '_B_speaks'); ?>
				<?= Html::activeHiddenInput($model, $pos . '_irregular'); ?>
				<?= $form->field($model, $pos . '_place', $fieldOption)
					->label($model->getResultLabel($debate, $pos))
					->textInput($textOption) ?>
				<?= $form->field($model, $pos . '_speaks', $fieldOption)
					->label("")
					->textInput($textOption) ?>
			</div>
		<? endforeach; ?>
	</div>

	<?= Html::activeHiddenInput($model, "confirmed", ["value" => "true"]); ?>
	<hr>

	<div class="row">
		<div class="col-xs-5">
			<?= Html::a(Html::icon("trash") . "&nbsp;" . Yii::t('app', 'start over'), ["create", "id" => $debate->id, "tournament_id" => $debate->tournament_id], ['class' => 'btn btn-default btn-block']) ?>
		</div>
		<div class="col-xs-7">
			<?= Html::submitButton(Yii::t('app', 'Make it so!') . "&nbsp;" . Html::icon("send"), ['class' => 'btn btn-success btn-block', 'autofocus' => 'autofocus']) ?>
		</div>
	</div>

	<?php ActiveForm::end(); ?>

</div>
