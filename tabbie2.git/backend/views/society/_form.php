<?php

	use yii\helpers\Html;
	use yii\widgets\ActiveForm;
	use yii\helpers\Url;
	use kartik\select2\Select2;
	use yii\web\JsExpression;

	/* @var $this yii\web\View */
	/* @var $model common\models\Society */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="society-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'fullname')->textInput(['maxlength' => 255]) ?>

	<?= $form->field($model, 'abr')->textInput(['maxlength' => 45]) ?>

	<?= $form->field($model, 'city')->textInput(['maxlength' => 255]) ?>

	<?
		$countryListURL = Url::to(['society/countries']);

		// Script to initialize the selection based on the value of the select2 element
		$initCountryScript = <<< SCRIPT
function (element, callback) {
    var id=\$(element).val();
    if (id !== "") {
        \$.ajax("{$countryListURL}?id=" + id, {
        dataType: "json"
        }).done(function(data) { callback(data.results);});
    }
}
SCRIPT;

		echo $form->field($model, 'country_id')->widget(Select2::classname(), [
			'options' => ['placeholder' => 'Search for a country ...'],
			'addon' => [
				"prepend" => [
					"content" => '<i class="glyphicon glyphicon-tower"></i>'
				],
			],
			'pluginOptions' => [
				'allowClear' => true,
				'minimumInputLength' => 1,
				'ajax' => [
					'url' => $countryListURL,
					'dataType' => 'json',
					'data' => new JsExpression('function(term,page) { return {search:term}; }'),
					'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
				],
				'initSelection' => new JsExpression($initCountryScript)
			],
		]);
	?>
	<div class="form-group">
		<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
