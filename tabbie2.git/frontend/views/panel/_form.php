<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;
use kartik\widgets\Select2;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Panel */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="panel-form">

	<?php $form = ActiveForm::begin(); ?>

	<?

	$url = Url::to(['adjudicator/list', "tournament_id" => $model->tournament_id]);

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

	$first = true;
	foreach ($model->set_adjudicators as $adj_k => $adj_v) {

		echo Select2::widget([
				'model' => $model,
				'attribute' => 'set_adjudicators[' . $adj_k . ']',
				'options' => [
					'placeholder' => ($first) ? Yii::t("app", 'Add Chair ...') : Yii::t("app", 'Add Wing ...'),
				],
				'pluginOptions' => [
					'allowClear' => true,
					'multiple' => false,
					'minimumInputLength' => 3,
					'ajax' => [
						'url' => $url,
						'dataType' => 'json',
						'data' => new JsExpression('function(term,page) { return {search:term}; }'),
						'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
					],
					'initSelection' => new JsExpression($initScript)
				],
			]) . "<br>";

		$first = false;
	}


	?>

	<div class="form-group">
		<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
