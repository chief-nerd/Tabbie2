<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model common\models\Society */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="society-form">

	<h2>
		<?php echo Yii::t("app", "Hey cool! You entered an unknown Society!") ?>
	</h2>

	<p>
		<?= Yii::t("app", "Before we can register you can you please complete the information about your Society:") ?>
	</p>

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'fullname')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'abr')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>

	<?
	$country_list = Url::to(['society/list-country']);
	// Script to initialize the selection based on the value of the select2 element
	$initCountryScript = <<< SCRIPT
function (element, callback) {
    var id=\$(element).val();
    if (id !== "") {
        \$.ajax("{$country_list}?cid=" + id, {
        dataType: "json"
        }).done(function(data) { callback(data.results);});
    }
}
SCRIPT;
	?>

	<?= $form->field($model, 'country_id')->widget(Select2::classname(), [
		'options' => [
			'placeholder' => Yii::t("app", 'Search for a country ...'),
			'multiple' => false,
		],
		'pluginOptions' => [
			'allowClear' => true,
			'minimumInputLength' => 3,
			'ajax' => [
				'url' => $country_list,
				'dataType' => 'json',
				'data' => new JsExpression('function(term,page) { return {search:term}; }'),
				'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
			],
			'initSelection' => new JsExpression($initCountryScript),
		],
	]) ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('app', 'Add new Society'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>