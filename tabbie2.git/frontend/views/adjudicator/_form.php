<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\StarRating;
use kartik\slider\Slider;
use common\models\Adjudicator;

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

	echo $form->field($model, 'user_id')->widget(Select2::classname(), [
            'options' => ['placeholder' => Yii::t("app", 'Search for a {object} ...', [
                    'object' => Yii::t("app", "User")
            ])],
		'addon'         => [
			"prepend" => [
				"content" => \kartik\helpers\Html::icon("user")
			],
		],
		'pluginOptions' => [
			'allowClear'    => true,
			'minimumInputLength' => 3,
			'ajax'          => [
				'url'     => $url,
				'dataType' => 'json',
				'data'    => new JsExpression('function(term,page) { return {search:term}; }'),
				'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
			],
			'initSelection' => new JsExpression($initScript)
		],
	]);
	?>
	<?
	echo $form->field($model, 'society_id')->widget(Select2::classname(), [
		'options'       => [
                'placeholder' => Yii::t("app", 'Search for a {object} ...', [
                        'object' => Yii::t("app", "Society")
                ]),
			'multiple' => false,
		],
		'addon'         => [
			"prepend" => [
				"content" => \kartik\helpers\Html::icon("tower")
			],
		],
		'pluginOptions' => [
			'allowClear'    => true,
			'minimumInputLength' => 3,
			'ajax'          => [
				'url'     => $urlSocietyList,
				'dataType' => 'json',
				'data'    => new JsExpression('function(term,page) { return {search:term}; }'),
				'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
			],
			'initSelection' => new JsExpression($initSocietyScript)
		],
	]);
	?>

	<?= $form->field($model, 'can_chair')->checkbox(); ?>

	<?= $form->field($model, 'are_watched')->checkbox(); ?>

	<?= $form->field($model, 'active')->checkbox(); ?>

	<?= $form->field($model, 'breaking')->checkbox(); ?>

	<?
	$jsExpression = "function(val) { ";
	for ($i = 0; $i <= (Adjudicator::MAX_RATING / 10) + 1; $i++) {
		$jsExpression .= "if (val < " . ($i * 10) . ") { return '" . Adjudicator::getStrengthLabel($i * 10 - 1) . " ('+val+')'; } ";
	}
	$jsExpression .= "}";

	echo $form->field($model, 'strength')->widget(\kartik\slider\Slider::className(), [
		'handleColor'   => Slider::TYPE_INFO,
		'sliderColor'   => Slider::TYPE_PRIMARY,
		'pluginOptions' => [
			'min'       => 0,
			'max'       => Adjudicator::MAX_RATING,
			'step'      => 1,
			'tooltip'   => 'always',
			'formatter' => new JsExpression($jsExpression)
		]
	]);
	/*[
	"pluginOptions" => [
		"stars" => 8,
		"min" => 0,
		"max" => 9,
		"step" => 1,
		"size" => "md",
		"starCaptions" => common\models\Adjudicator::translateStrength(),
		"starCaptionClasses" => common\models\Adjudicator::starLabels(),
	],
])*/

	//echo $form->field($model, 'strength')->textInput();
	?>

	<div class="form-group">
		<?= Html::submitButton(\kartik\helpers\Html::icon("floppy-disk") . "&nbsp;" . ($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update')), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
