<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\PanelSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="panel-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<?= $form->field($model, 'id') ?>

	<?= $form->field($model, 'strength') ?>

	<?= $form->field($model, 'time') ?>

	<?= $form->field($model, 'tournament_id') ?>

	<?= $form->field($model, 'used') ?>

	<?php // echo $form->field($model, 'is_preset') ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
