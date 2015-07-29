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

$this->registerJs('$(".field-userclash-reason textarea").keypress(function(){
	var max = 200;
    if(this.value.length > max){
        return false;
    }
    $(".field-userclash-reason .help-block").html("Remaining characters : " +(max - this.value.length));
});');
?>

<div class="clash-form">

	<?php $form = ActiveForm::begin([
		"enableClientValidation" => false,
		"id"                     => "clash-create-form"
	]); ?>

	<?
	$urlUserList = Url::to(['user/list']);

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

	echo $form->field($model, 'clash_with')->hint(Yii::t("app", "Not every debater is yet in the system. :)"))->widget(Select2::classname(), [
		'options'       => ['placeholder' => Yii::t("app", 'Search for a user ...')],
		'addon'         => [
			"prepend" => [
				"content" => \kartik\helpers\Html::icon("user")
			],
		],
		'pluginOptions' => [
			'allowClear'         => true,
			'minimumInputLength' => 3,
			'ajax'               => [
				'url'      => $urlUserList,
				'dataType' => 'json',
				'data'     => new JsExpression('function(term,page) { return {search:term}; }'),
				'results'  => new JsExpression('function(data,page) { return {results:data.results}; }'),
			],
			'initSelection'      => new JsExpression($initUserScript)
		],
		'pluginEvents'  => [
			"select2-selecting" => "function(obj) { console.log(obj); }",
		],
	]);
	?>

	<?=
	$form->field($model, 'reason')->textarea()->hint(Yii::t("app", "Please give the CAs a rough understanding why you think you are clashed from the above person."));
	?>
	<span id='remainingC'></span>

	<div class="form-group">
		<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
