<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\LanguageMaintainer */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="language-maintainer-form">

	<?php $form = ActiveForm::begin(); ?>

	<?
	$url = Yii::$app->urlManagerFrontend->createAbsoluteUrl("user/list");

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

	echo $form->field($model, 'user_id')->widget(\kartik\widgets\Select2::classname(), [
		'options' => ['placeholder' => Yii::t("app", 'Search for a {object} ...', [
			'object' => Yii::t("app", "User")
		])],
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
				'data' => new \yii\web\JsExpression('function(term,page) { return {search:term}; }'),
				'results' => new \yii\web\JsExpression('function(data,page) { return {results:data.results}; }'),
			],
			'initSelection' => new \yii\web\JsExpression($initScript)
		],
	]);
	?>

	<?= $form->field($model, 'language_language')->textInput(['maxlength' => true]) ?>

	<div class="form-group">
		<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
