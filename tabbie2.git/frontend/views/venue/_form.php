<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Venue */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="venue-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= Html::activeHiddenInput($model, "tournament_id") ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => 100]) ?>

	<?= $form->field($model, 'active')->checkbox() ?>

	<div class="form-group">
		<?= Html::submitButton(\kartik\helpers\Html::icon("floppy-disk") . "&nbsp" . ($model->isNewRecord ? Yii::t('app', 'Create') : (Yii::t('app', 'Update'))), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
