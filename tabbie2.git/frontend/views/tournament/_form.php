<?php

use kartik\widgets\ActiveForm;
use kartik\widgets\DateTimePicker;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Tournament */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tournament-form">

	<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

	<?= Html::activeHiddenInput($model, 'convenor_user_id', ["value" => Yii::$app->user->id]) ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => 100, 'placeholder' => 'My super awesome IV']) ?>

	<?= $form->field($model, 'hosted_by_id', [
		'addon' => ['prepend' => ['content' => "<i class=\"glyphicon glyphicon-education\"></i>"]]
	])->dropDownList($model->getSocietiesOptions()) ?>

	<?= $form->field($model, 'tabmaster_user_id', [
		'addon' => ['prepend' => ['content' => "<i class=\"glyphicon glyphicon-user\"></i>"]]
	])->dropDownList($model->getTabmasterOptions()) ?>

	<?=
	$form->field($model, 'start_date', [
		'addon' => ['prepend' => ['content' => "<i class=\"glyphicon glyphicon-calendar\"></i>"]]
	])->widget(DateTimePicker::classname(), [
		'type' => DateTimePicker::TYPE_INPUT,
		'options' => ['placeholder' => 'Enter start date / time ...'],
		'pluginOptions' => [
			'format' => 'yyyy-mm-dd hh:ii',
			'startDate' => date("Y-m-d H:i"),
			'autoclose' => true,
		]
	]);
	?>

	<?=
	$form->field($model, 'end_date', [
		'addon' => ['prepend' => ['content' => "<i class=\"glyphicon glyphicon-calendar\"></i>"]]
	])->widget(DateTimePicker::classname(), [
		'type' => DateTimePicker::TYPE_INPUT,
		'options' => ['placeholder' => 'Enter the end date / time ...'],
		'pluginOptions' => [
			'format' => 'yyyy-mm-dd hh:ii',
			'startDate' => date("Y-m-d H:i"),
			'autoclose' => true,
		]
	]);
	?>

	<?= $form->field($model, 'expected_rounds')
	         ->dropDownList([1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9]) ?>

	<?= $form->field($model, 'tabAlgorithmClass', [
		'addon' => ['prepend' => ['content' => "<i class=\"glyphicon glyphicon-flash\"></i>"]]
	])->dropDownList($model->getTabAlgorithmOptions()) ?>

	<?= $form->field($model, 'has_esl')->checkbox() ?>
	<?= $form->field($model, 'has_final')->checkbox() ?>
	<?= $form->field($model, 'has_semifinal')->checkbox() ?>
	<?= $form->field($model, 'has_quarterfinal')->checkbox() ?>
	<?= $form->field($model, 'has_octofinal')->checkbox() ?>


	<div class="row">
		<div class="col-sm-2">
			<?= $model->getLogoImage(150, 150, ["id" => "previewImageUpload"]) ?>
		</div>
		<div class="col-sm-10">
			<?= $form->field($model, 'logo')->fileInput() ?>
			<script>
				var s = document.getElementById('tournament-logo');
				s.onchange = function (event) {
					document.getElementById('previewImageUpload').src = URL.createObjectURL(event.target.files[0]);
				}
			</script>
		</div>
	</div>
	<br>

	<div class="form-group row">
		<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
