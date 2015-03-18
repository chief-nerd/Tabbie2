<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\widgets\Select2;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model common\models\Strikes */
/* @var $form yii\widgets\ActiveForm */

$tournament = $this->context->_getContext();
?>

<div class="strikes-form">

	<?php $form = ActiveForm::begin(); ?>

	<?
	$urlTeamList = Url::to(['team/list', "tournament_id" => $tournament->id]);

	// Script to initialize the selection based on the value of the select2 element
	$initTeamScript = <<< SCRIPT
function (element, callback) {
    var id=\$(element).val();
    if (id !== "") {
        \$.ajax("{$urlTeamList}?id=" + id + "&tournament_id=" + $tournament->id, {
        dataType: "json"
        }).done(function(data) { callback(data.results);});
    }
}
SCRIPT;
	?>

	<?= $form->field($model, 'team_id')->widget(Select2::classname(), [
		'options' => ['placeholder' => 'Search for a Team ...'],
		'pluginOptions' => [
			'allowClear' => true,
			'minimumInputLength' => 2,
			'ajax' => [
				'url' => $urlTeamList,
				'dataType' => 'json',
				'data' => new JsExpression('function(term,page) { return {search:term}; }'),
				'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
			],
			'initSelection' => new JsExpression($initTeamScript)
		],
	]);
	?>

	<?
	$urlAdjList = Url::to(['adjudicator/list', "tournament_id" => $tournament->id]);

	// Script to initialize the selection based on the value of the select2 element
	$initAdjScript = <<< SCRIPT
function (element, callback) {
    var id=\$(element).val();
    if (id !== "") {
        \$.ajax("{$urlAdjList}?id=" + id + "&tournament_id=" + $tournament->id, {
        dataType: "json"
        }).done(function(data) { callback(data.results);});
    }
}
SCRIPT;
	?>

	<?= $form->field($model, 'adjudicator_id')->widget(Select2::classname(), [
		'options' => ['placeholder' => 'Search for an Adjudicator ...'],
		'pluginOptions' => [
			'allowClear' => true,
			'minimumInputLength' => 2,
			'ajax' => [
				'url' => $urlAdjList,
				'dataType' => 'json',
				'data' => new JsExpression('function(term,page) { return {search:term}; }'),
				'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
			],
			'initSelection' => new JsExpression($initAdjScript)
		],
	]);
	?>

	<div class="form-group">
		<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
