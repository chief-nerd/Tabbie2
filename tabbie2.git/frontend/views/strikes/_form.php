<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Strikes */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="strikes-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'team_id')->textInput() ?>

	<?= $form->field($model, 'adjudicator_id')->textInput() ?>

	<?= $form->field($model, 'approved')->textInput() ?>

	<div class="form-group">
		<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
