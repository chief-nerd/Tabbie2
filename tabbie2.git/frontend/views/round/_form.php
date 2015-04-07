<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Round */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="round-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'number')->textInput(["readonly" => "readonly"]); ?>

	<?= Html::activeHiddenInput($model, 'tournament_id') ?>

	<?= $form->field($model, 'motion')->textarea(['rows' => 2]) ?>

	<?= $form->field($model, 'infoslide')->textarea(['rows' => 6]) ?>

	<?= $form->field($model, 'published')->checkbox(); ?>

	<div class="form-group">
		<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'),
			[
				'class' => $model->isNewRecord ? 'btn btn-success loading' : 'btn btn-primary',
			]) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
