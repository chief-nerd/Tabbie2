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

	<?php $form = ActiveForm::begin(); ?>
	<?=
	$form->field($model, 'status')->dropDownList(User::getStatusOptions());
	?>
	<?= $form->field($model, 'username')->textInput(['maxlength' => 255]) ?>

	<?= $form->field($model, 'givenname')->textInput(['maxlength' => 255]) ?>

	<?= $form->field($model, 'surename')->textInput(['maxlength' => 255]) ?>

	<?= $form->field($model, 'email', ['addon' => ['prepend' => ['content' => '@']]])
	         ->textInput(['maxlength' => 255]) ?>

	<?
	$urlUserList = Url::to(['user/societies']);

	// Script to initialize the selection based on the value of the select2 element
	$initUserScript = <<< SCRIPT
function (element, callback) {
    var id=\$(element).val();
    if (id !== "") {
        \$.ajax("{$urlUserList}?id=" + id, {
        dataType: "json"
        }).done(function(data) { callback(data.results);});
    }
}
SCRIPT;

	echo $form->field($model, 'societies_id')->widget(Select2::classname(), [
		'options' => [
			'placeholder' => 'Search for a societies ...',
			'multiple' => true,
		],
		'addon' => [
			"prepend" => [
				"content" => '<i class="glyphicon glyphicon-education"></i>'
			],
		],
		'pluginOptions' => [
			'allowClear' => true,
			'minimumInputLength' => 3,
			'ajax' => [
				'url' => $urlUserList,
				'dataType' => 'json',
				'data' => new JsExpression('function(term,page) { return {search:term}; }'),
				'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
			],
			'initSelection' => new JsExpression($initUserScript)
		],
	]);
	?>

	<?=
	$form->field($model, 'role')->dropDownList(User::getRoleOptions());
	?>

	<?= $form->field($model, 'picture')->fileInput() ?>

	<div class="form-group">
		<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
