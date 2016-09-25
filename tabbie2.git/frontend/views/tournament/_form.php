<?php

use kartik\widgets\ActiveForm;
use kartik\widgets\DateTimePicker;
use yii\helpers\Html;
use \kartik\widgets\Select2;
use yii\web\JsExpression;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Tournament */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tournament-form">

	<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

	<?= $form->field($model, 'name')
		->textInput(['maxlength' => 100, 'placeholder' => Yii::t("app", 'My super awesome IV ... e.g. Vienna IV')]); ?>

	<?
	$urlSocietyList = Url::to(['society/list']);

	// Script to initialize the selection based on the value of the select2 element
	$initSocietyScript = <<< SCRIPT
function (element, callback) {
    var id=\$(element).val();
    if (id !== "") {
        \$.ajax("{$urlSocietyList}?sid=" + id, {
        dataType: "json"
        }).done(function(data) { callback(data.results);});
    }
}
SCRIPT;

	echo $form->field($model, 'hosted_by_id')->widget(Select2::className(), [
		'options' => [
			'placeholder' => Yii::t("app", 'Search for a society ...')
		],
		'addon'         => [
			"prepend" => [
				"content" => \kartik\helpers\Html::icon("education")
			],
		],
		'pluginOptions' => [
			'allowClear'         => false,
			'minimumInputLength' => 3,
			'ajax'               => [
				'url'      => $urlSocietyList,
				'dataType' => 'json',
				'data'     => new JsExpression('function(term,page) { return {search:term}; }'),
				'results'  => new JsExpression('function(data,page) { return {results:data.results}; }'),
			],
			'initSelection'      => new JsExpression($initSocietyScript)
		],
	]);
	?>

	<?
	$urlUserList = Url::to(['user/list']);

	echo $form->field($model, 'convenors')->widget(Select2::className(), [
		'initValueText' => \yii\helpers\ArrayHelper::map($model->convenors, "id", "name"),
		'addon'         => [
			'prepend' => [
				'content' => \kartik\helpers\Html::icon('king')
			],
		],
		'options'       => [
			'placeholder' => Yii::t("app", 'Select the Convenors ...'),
		],
		'pluginOptions' => [
			'multiple'           => true,
			'minimumInputLength' => 3,
			'ajax'               => [
				'url'      => $urlUserList,
				'dataType' => 'json',
				'data'     => new JsExpression('function(params) { return {q:params.term}; }')
			],
			'escapeMarkup'       => new JsExpression('function (markup) { return markup; }'),
		],
	]);
	?>

	<?=
	$form->field($model, 'start_date', [
		'addon' => ['prepend' => ['content' => "<i class=\"glyphicon glyphicon-calendar\"></i>"]]
	])->widget(DateTimePicker::classname(), [
		'type'          => DateTimePicker::TYPE_INPUT,
		'options'       => ['placeholder' => Yii::t("app", 'Enter start date / time ...')],
		'pluginOptions' => [
			'format' => 'yyyy-mm-dd hh:ii',
			'startDate' => date("Y-m-d H:i"),
			'autoclose' => true,
		]
	]);
	?>

	<?=
	$form->field($model, 'end_date', [
		'addon' => ['prepend' => ['content' => "<i class=\"glyphicon glyphicon-calendar\"></i>"]]
	])->widget(DateTimePicker::classname(), [
		'type'          => DateTimePicker::TYPE_INPUT,
		'options'       => ['placeholder' => Yii::t("app", 'Enter the end date / time ...')],
		'pluginOptions' => [
			'format' => 'yyyy-mm-dd hh:ii',
			'startDate' => date("Y-m-d H:i"),
			'autoclose' => true,
		]
	]);
	?>

	<?
	echo $form->field($model, 'timezone', [
		'addon' => ['prepend' => ['content' => "<i class=\"glyphicon glyphicon-calendar\"></i>"]]
	])->dropDownList(\common\models\Tournament::getTimeZones());
	?>

	<?
	$urlUserList = Url::to(['user/list']);

	echo $form->field($model, 'cAs')->label(Yii::t("app", "Chief Adjudicators"))->widget(Select2::className(), [
		'initValueText' => \yii\helpers\ArrayHelper::map($model->cAs, "id", "name"),
		'addon'         => [
			'prepend' => [
				'content' => \kartik\helpers\Html::icon('star')
			],
		],
		'options'       => [
			'placeholder' => Yii::t("app", 'Choose your CAs ...'),
		],
		'pluginOptions' => [
			'multiple'           => true,
			'minimumInputLength' => 3,
			'ajax'               => [
				'url'      => $urlUserList,
				'dataType' => 'json',
				'data'     => new JsExpression('function(params) { return {q:params.term}; }')
			],
			'escapeMarkup'       => new JsExpression('function (markup) { return markup; }'),
		],
	]);
	?>

	<?
	$urlUserList = Url::to(['user/list']);

	echo $form->field($model, 'tabmasters')->widget(Select2::className(), [
		'initValueText' => \yii\helpers\ArrayHelper::map($model->tabmasters, "id", "name"),
		'addon'         => [
			'prepend' => [
				'content' => \kartik\helpers\Html::icon('sunglasses')
			],
		],
		'options'       => [
			'placeholder' => Yii::t("app", 'Choose your Tab Director ...'),
		],
		'pluginOptions' => [
			'multiple'           => true,
			'minimumInputLength' => 3,
			'ajax'               => [
				'url'      => $urlUserList,
				'dataType' => 'json',
				'data'     => new JsExpression('function(params) { return {q:params.term}; }')
			],
			'escapeMarkup'       => new JsExpression('function (markup) { return markup; }'),
		],
	]);
	?>

	<?= $form->field($model, 'expected_rounds')
		->dropDownList([1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9]) ?>

	<?= $form->field($model, 'tabAlgorithmClass', [
		'addon' => ['prepend' => ['content' => "<i class=\"glyphicon glyphicon-flash\"></i>"]]
	])->dropDownList($model->getTabAlgorithmOptions()) ?>

	<div class="panel panel-default">
		<div class="panel-body">
			<?= $form->field($model, 'has_esl')->checkbox() ?>
			<?= $form->field($model, 'has_efl')->checkbox() ?>
			<?= $form->field($model, 'has_novice')->checkbox() ?>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-body">
			<?= $form->field($model, 'has_final')->checkbox() ?>
			<?= $form->field($model, 'has_semifinal')->checkbox() ?>
			<?= $form->field($model, 'has_quarterfinal')->checkbox() ?>
			<?= $form->field($model, 'has_octofinal')->checkbox() ?>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-2">
			<?= $model->getLogoImage(150, 150, ["id" => "previewImageUpload"]) ?>
		</div>
		<div class="col-sm-10">
			<?= $form->field($model, 'logo')->fileInput() ?>
			<script>
				var s = document.getElementById('tournament-logo');
				s.onchange = function (event) {
					document.getElementById('previewImageUpload').src = URL.createObjectURL(event.target.files[0]);
				}
			</script>
		</div>
	</div>
	<br>

	<div class="form-group row">
		<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success btn-block' : 'btn btn-primary btn-block']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
