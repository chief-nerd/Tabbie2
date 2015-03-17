<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model common\models\EnergyConfig */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="energy-config-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= Html::hiddenInput('tournament_id', $model->tournament_id); ?>

	<?= $form->field($model, 'key')->textInput(['maxlength' => 100, "disabled" => "disabled"]) ?>

	<?= $form->field($model, 'label')->textInput(['maxlength' => 45, "disabled" => "disabled"]) ?>

	<?= $form->field($model, 'value')->widget(MaskedInput::className(), [
		'name' => 'value',
		'mask' => '9',
		'clientOptions' => ['repeat' => 4, 'greedy' => false]
	]); ?>

	<div class="form-group">
		<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
