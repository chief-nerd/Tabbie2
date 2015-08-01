<?php
/**
 * badge_select.php File
 *
 * @package  Tabbie2
 * @author   jareiter
 * @version
 */
use kartik\widgets\ActiveForm;
use kartik\helpers\Html;

$this->title = Yii::t('app', 'Generate Badges');
/** @var \common\models\Tournament $tournament */
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div id="barcodeForm">
	<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

	<? if ($tournament->badge): ?>
		<div class="row">
			<div class="col-xs-12">
				<img src="<?= $tournament->getBadge() ?>" width="300px">
			</div>
		</div>
	<? endif; ?>

	<div class="row">
		<div class="col-xs-2">
			<?= Html::label("Badge Background:", "badge"); ?>
		</div>
		<div class="col-xs-10">
			<?= Html::fileInput("badge", '') ?>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-2">
			<?= Html::label("Use Background:", "use"); ?>
		</div>
		<div class="col-xs-10">
			<?= Html::checkbox("use", true) ?>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-2">
			<?= Html::label("Paper Format", "paper"); ?>
		</div>
		<div class="col-xs-10">
			<?= Html::dropDownList("paper", "A6", ["A4" => "A4 (2x2)", "A6" => "A6 (1)"]) ?>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-2">
			<?= Html::label("Paper Margin", "margin"); ?>
		</div>
		<div class="col-xs-10">
			<?= Html::textInput("margin", "4") ?>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-2">
			<?= Html::label("Paper Border CSS", "border"); ?>
		</div>
		<div class="col-xs-10">
			<?= Html::textInput("border", "1px solid white") ?>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-2">
			<?= Html::label("Code height", "height"); ?>
		</div>
		<div class="col-xs-10">
			<?= Html::textInput("height", 70) ?>
		</div>
	</div>

	<hr>

	<div class="row">
		<div class="col-xs-2">
			<?= Html::label("Single Person", "person"); ?>
		</div>
		<?
		$urlUserList = \yii\helpers\Url::to(['user/list']);

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
		<div class="col-xs-10">
			<?= \kartik\widgets\Select2::widget([
				'name'          => 'person',
				'options'       => ['placeholder' => Yii::t("app", 'Only do for User ...')],
				'addon'         => [
					"prepend" => [
						"content" => \kartik\helpers\Html::icon("user")
					],
				],
				'pluginOptions' => [
					'multiple'           => true,
					'allowClear'         => true,
					'minimumInputLength' => 3,
					'ajax'               => [
						'url'      => $urlUserList,
						'dataType' => 'json',
						'data'     => new \yii\web\JsExpression('function(term,page) { return {search:term}; }'),
						'results'  => new \yii\web\JsExpression('function(data,page) { return {results:data.results}; }'),
					],
					'initSelection'      => new \yii\web\JsExpression($initUserScript)
				],
				'pluginEvents'  => [
					"select2-selecting" => "function(obj) { console.log(obj); }",
				],
			]); ?>
		</div>
	</div>
	<br>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('app', 'Print Badges'),
			[
				'class' => 'btn btn-success',
			]) ?>
	</div>
	<?php ActiveForm::end(); ?>
</div>
