<?php
/**
 * barcode_select.php File
 *
 * @package  Tabbie2
 * @author   jareiter
 * @version
 */
use kartik\widgets\ActiveForm;
use kartik\helpers\Html;
use yii\web\JsExpression;
use yii\helpers\Url;

?>
<div id="barcodeForm">
	<?php $form = ActiveForm::begin(); ?>

	<div class="row">
		<div class="col-xs-12">
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
			?>
			<?= \kartik\select2\Select2::widget([
				'options' => ['placeholder' => Yii::t("app", 'Search for a user ... or leave blank')],
				'name' => 'userID',
				'addon' => [
					"prepend" => [
						"content" => \kartik\helpers\Html::icon("user")
					]
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
					'initSelection' => new JsExpression($initUserScript),
				]
			]); ?>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-2">
			<?= Html::label("Offset:", "offset"); ?>
		</div>
		<div class="col-xs-10">
			<?
			$offset = ['none'];
			for ($i = 1; $i < 20; $i++) {
				$offset[] = $i;
			}
			?>
			<?= Html::dropDownList("offset", '', $offset) ?>
		</div>
	</div>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('app', 'Print Barcodes'),
			[
				'class' => 'btn btn-success',
			]) ?>
	</div>
	<?php ActiveForm::end(); ?>
</div>