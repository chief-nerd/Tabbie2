<?php

use kartik\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Team;

/* @var $this yii\web\View */
/* @var $model common\models\Result */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="result-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= Html::activeHiddenInput($model, 'debate_id') ?>
	<?
	/* @var $debate Debate */
	$debate = $model->debate;
	$cols = "col-xs-12 col-sm-6";
	$fieldOption = [
		"template" => "{label} {input}\n{hint}\n{error}",
	];
	$textOption = ["size" => 2, "maxlength" => 2];
	?>

	<div class="row">
		<? foreach (Team::getPos() as $index => $pos): ?>
			<div class="<?= $cols ?>">
				<h3><?= Team::getPosLabel($index) ?></h3>
				<h4><?= $debate->{$pos . "_team"}->name ?></h4>
				<?
				$A = $debate->{$pos . "_team"}->speakerA;
				$B = $debate->{$pos . "_team"}->speakerB;
				?>
				<?= $form->field($model, $pos . '_A_speaks', $fieldOption)
					->label(($A) ? $A->name : \common\models\User::NONE)
					->textInput($textOption) ?>
				<?= $form->field($model, $pos . '_B_speaks', $fieldOption)
					->label(($B) ? $B->name : \common\models\User::NONE)
					->textInput($textOption) ?>
			</div>
		<? endforeach; ?>
	</div>

	<hr>
	<div id="irregular_options" class="collapse">
		<h3>Irregular Options</h3>

		<div class="row">
			<div class="<?= $cols ?>">
				<?= $form->field($model, "og_irregular")->dropDownList(\common\models\Team::getIrregularOptions()) ?>
			</div>
			<div class="<?= $cols ?>">
				<?= $form->field($model, "oo_irregular")->dropDownList(\common\models\Team::getIrregularOptions()) ?>
			</div>
		</div>
		<div class="row">
			<div class="<?= $cols ?>">
				<?= $form->field($model, "cg_irregular")->dropDownList(\common\models\Team::getIrregularOptions()) ?>
			</div>
			<div class="<?= $cols ?>">
				<?= $form->field($model, "co_irregular")->dropDownList(\common\models\Team::getIrregularOptions()) ?>
			</div>
		</div>
		<hr>
	</div>
	<div class="row">
		<div class="col-xs-5">
			<?= Html::Button(Yii::t('app', 'Options') . "&nbsp;" . Html::icon("chevron-down"), [
				'class'         => 'btn btn-default btn-block',
				'data-toggle'   => "collapse",
				'data-target'   => "#irregular_options",
				'aria-expanded' => "false",
				'aria-controls' => "irregular_options",
			]) ?>
		</div>
		<div class="col-xs-7">
			<?= Html::submitButton(Yii::t('app', 'Continue') . "&nbsp;" . Html::icon("chevron-right"), ['class' => 'btn btn-success btn-block']) ?>
		</div>
	</div>

	<?php ActiveForm::end(); ?>

</div>
