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

	<?= $form->field($model, 'email', ['addon' => ['prepend' => ['content' => '<b>@</b>']]])
		->textInput(['maxlength' => 255]) ?>

	<div class="row">
		<div class="col-sm-6">
			<?= $form->field($model, 'password', ['addon' => ['prepend' => ['content' => "<span class='glyphicon glyphicon-lock'></span>"]]])
				->passwordInput(['maxlength' => 255]) ?>

		</div>
		<div class="col-sm-6">
			<?= $form->field($model, 'password_repeat')
				->passwordInput(['maxlength' => 255]) ?>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-6">
			<?= $form->field($model, 'givenname')->textInput(['maxlength' => 255]) ?>
		</div>
		<div class="col-sm-6">
			<?= $form->field($model, 'surename')->textInput(['maxlength' => 255]) ?>

		</div>
	</div>

	<div class="row">
		<div class="col-sm-2">
			<?= $model->getPictureImage(150, 150, ["id" => "previewImageUpload"]) ?>
		</div>
		<div class="col-sm-4">
			<?= $form->field($model, 'picture')->fileInput() ?>
			<script>
				var s = document.getElementById('user-picture');
				s.onchange = function (event) {
					document.getElementById('previewImageUpload').src = URL.createObjectURL(event.target.files[0]);
				}
			</script>
		</div>
		<div class="col-sm-6">
			<?= $form->field($model, 'gender')->dropDownList(\common\models\User::genderOptions()) ?>
		</div>
		<div class="col-sm-6 col-sm-push-4">
            <? //echo $form->field($model, 'language')->dropDownList(\common\models\User::languageOptions()) ?>
		</div>
	</div>

	<div class="form-group">
		<?= Html::a(Yii::t('app', 'Cancel'), ["view", "id" => $model->id], ["class" => "btn btn-default"]) ?>
		<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create User') : (\kartik\helpers\Html::icon("send") . "&nbsp" . Yii::t('app', 'Update User')), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
