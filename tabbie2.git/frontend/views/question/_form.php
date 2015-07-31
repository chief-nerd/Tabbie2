<?php

use yii\widgets\ActiveForm;
use kartik\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\question */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="question-form">

	<?php $form = ActiveForm::begin(); ?>

	<div class="row">
		<?= $form->field($model, 'apply_T2C')->checkbox() ?>
		<?= $form->field($model, 'apply_C2W')->checkbox() ?>
		<?= $form->field($model, 'apply_W2C')->checkbox() ?>
	</div>
	<div class="row">
		<?= $form->field($model, 'text')->textInput(['maxlength' => 255]) ?>
	</div>
	<div class="row">
		<?= $form->field($model, 'type')->dropDownList($model->getTypeOptions()) ?>
	</div>

	<div class="row">
		<?= $form->field($model, 'param')->textarea() ?>
	</div>

	<div class="row form-group">
		<?= Html::submitButton(Html::icon("floppy-disk") . "&nbsp;" . ($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update')), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
