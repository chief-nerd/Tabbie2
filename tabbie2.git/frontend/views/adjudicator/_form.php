<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\StarRating;

/* @var $this yii\web\View */
/* @var $model common\models\Adjudicator */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="adjudicator-form">

	<?php $form = ActiveForm::begin(); ?>

	<?
	$url = Url::to(['user/list']);

	// Script to initialize the selection based on the value of the select2 element
	$initScript = <<< SCRIPT
function (element, callback) {
    var id=\$(element).val();
    if (id !== "") {
        \$.ajax("{$url}?id=" + id, {
        dataType: "json"
        }).done(function(data) { callback(data.results);});
    }
}
SCRIPT;

	$Societyurl = Url::to(['society/list', "user_id" => $model->user_id]);

	// Script to initialize the selection based on the value of the select2 element
	$initSocietyScript = <<< SCRIPT
function (element, callback) {
    var id=\$(element).val();
    if (id !== "") {
        \$.ajax("{$Societyurl}?id=" + id, {
        dataType: "json"
        }).done(function(data) { callback(data.results);});
    }
}
SCRIPT;

	echo $form->field($model, 'user_id')->widget(Select2::classname(), [
		'options' => ['placeholder' => Yii::t("app", 'Search for a user ...')],
		'addon' => [
			"prepend" => [
				"content" => \kartik\helpers\Html::icon("user")
			],
		],
		'pluginOptions' => [
			'allowClear' => true,
			'minimumInputLength' => 3,
			'ajax' => [
				'url' => $url,
				'dataType' => 'json',
				'data' => new JsExpression('function(term,page) { return {search:term}; }'),
				'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
			],
			'initSelection' => new JsExpression($initScript)
		],
	]);
	?>
	<?
	echo $form->field($model, 'society_id')->widget(Select2::classname(), [
		'options' => [
			'placeholder' => Yii::t("app", 'Search for a societies ...'),
			'multiple' => true,
		],
		'addon' => [
			"prepend" => [
				"content" => \kartik\helpers\Html::icon("tower")
			],
		],
		'pluginOptions' => [
			'allowClear' => true,
			'minimumInputLength' => 3,
			'ajax' => [
				'url' => $Societyurl,
				'dataType' => 'json',
				'data' => new JsExpression('function(term,page) { return {search:term}; }'),
				'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
			],
			'initSelection' => new JsExpression($initSocietyScript)
		],
	]);
	?>

	<?=
	$form->field($model, 'can_chair')->checkbox();
	?>

	<?=
	$form->field($model, 'are_watched')->checkbox();
	?>

	<?=
	$form->field($model, 'strength')->widget(StarRating::className(), [
		"pluginOptions" => [
			"stars" => 8,
			"min" => 0,
			"max" => 9,
			"step" => 1,
			"size" => "md",
			"starCaptions" => common\models\Adjudicator::translateStrength(),
			"starCaptionClasses" => common\models\Adjudicator::starLabels(),
		],
	])
	?>

	<div class="form-group">
		<?= Html::submitButton(\kartik\helpers\Html::icon("floppy-disk") . "&nbsp;" . ($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update')), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
