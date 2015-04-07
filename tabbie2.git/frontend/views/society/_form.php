<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\DatePicker;

/* @var $this yii\web\View */
/* @var $model common\models\Society */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="society-form">

	<?php $form = ActiveForm::begin(); ?>

	<?
	$urlUserList = Url::to(['society/list', "user_id" => $model->user_id]);

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

	echo $form->field($model, 'society')->widget(Select2::classname(), [
		'options' => [
			'placeholder' => 'Search for a society ...',
			'multiple' => true,
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
	$form->field($model, 'starting')->widget(DatePicker::classname(), [
		'type' => DatePicker::TYPE_INPUT,
		'options' => ['placeholder' => 'Enter start date ...'],
		'pluginOptions' => [
			'autoclose' => true,
			'format' => 'yyyy-mm-dd'
		]
	]);
	?>

	<?
	if (!$model->isNewRecord)
		echo $form->field($model, 'ending')->widget(DatePicker::classname(), [
			'type' => DatePicker::TYPE_INPUT,
			'options' => ['placeholder' => 'Enter ending date if applicable ...'],
			'pluginOptions' => [
				'autoclose' => true,
				'format' => 'yyyy-mm-dd',
			]
		]);
	?>

	<div class="form-group">
		<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
