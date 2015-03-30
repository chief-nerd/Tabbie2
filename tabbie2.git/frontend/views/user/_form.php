<?php

use common\models\User;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

	<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

	<? $form->field($model, 'username')->textInput(['maxlength' => 255]) ?>

	<?= $form->field($model, 'givenname')->textInput(['maxlength' => 255]) ?>

	<?= $form->field($model, 'surename')->textInput(['maxlength' => 255]) ?>

	<?= $form->field($model, 'email', ['addon' => ['prepend' => ['content' => '<b>@</b>']]])
	         ->textInput(['maxlength' => 255]) ?>

	<?= $form->field($model, 'password', ['addon' => ['prepend' => ['content' => "<span class='glyphicon glyphicon-lock'></span>"]]])
	         ->passwordInput(['maxlength' => 255]) ?>

	<?= $form->field($model, 'picture')->fileInput() ?>

	<div class="form-group">
		<?= Html::a(Yii::t('app', 'Cancel'), ["view", "id" => $model->id], ["class" => "btn btn-default"]) ?>
		<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
