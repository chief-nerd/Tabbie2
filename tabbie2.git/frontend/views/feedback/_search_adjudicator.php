<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \yii\helpers\Url;
use kartik\widgets\Select2;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model common\models\search\FeedbackSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="feedback-search">

	<?php $form = ActiveForm::begin([
		'action' => ['adjudicator', "tournament_id" => $tournament->id],
		'method' => 'get',
		'id' => 'searchAdjudicatorForm'
	]);
	$url = Url::to(['adjudicator/list', "tournament_id" => $tournament->id]);

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
	?>

	<div class="row">
		<div class="col-sm-12">
			<?= $form->field($model, 'id')->widget(Select2::classname(), [
					'options' => ['placeholder' => Yii::t("app", 'Filter for an adjudicator ...')],
					'addon' => [
						"prepend" => [
							"content" => '<i class="glyphicon glyphicon-user"></i>'
						],
					],
					'pluginOptions' => [
						'allowClear' => true,
						'minimumInputLength' => 2,
						'ajax' => [
							'url' => $url,
							'dataType' => 'json',
							'data' => new JsExpression('function(term,page) { return {search:term}; }'),
							'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
						],
						'initSelection' => new JsExpression($initScript)
					],
					'pluginEvents' => [
						"change" => "function() { $('#searchAdjudicatorForm').submit() }",
					]
				]
			); ?>
		</div>
	</div>

	<?php ActiveForm::end(); ?>

</div>
