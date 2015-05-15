<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\UserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-search">

	<?php
	$form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]);
	?>

	<div class="row">
		<div class="col-xs-1"><?= $form->field($model, 'id') ?></div>
		<div class="col-xs-3"><?= $form->field($model, 'name') ?></div>
		<div class="col-xs-3"><?= $form->field($model, 'email') ?></div>
		<div class="col-xs-2"><?= $form->field($model, 'role')
		                               ->dropDownList(\common\models\User::getRoleOptions(true)) ?></div>
		<div class="form-group col-xs-3 text-right">
			<?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
			<?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
		</div>
	</div>

	<?php $form->end(); ?>

</div>
