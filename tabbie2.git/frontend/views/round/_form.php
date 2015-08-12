<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Round */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="round-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= Html::activeHiddenInput($model, 'label') ?>

	<?= Html::activeHiddenInput($model, 'tournament_id') ?>

	<?= $form->field($model, 'motion')->textarea(['rows' => 2]) ?>

	<?
	$urlTagSearch = \yii\helpers\Url::to(['motiontag/list']);
	$newDataScript = <<< SCRIPT
function (query) {
    return {
      id: query.term,
      text: query.term,
      tag: true
    }
  }
SCRIPT;

	echo $form->field($model, 'tags')->widget(\kartik\widgets\Select2::classname(), [
		'initValueText' => \yii\helpers\ArrayHelper::map($model->motionTags, "id", "name"),
		'options'       => [
			'placeholder' => Yii::t("app", 'Search for a Motion tag ...'),
		],
		'pluginOptions' => [
			'multiple'           => true,
			'minimumInputLength' => 2,
			'ajax'               => [
				'url'      => $urlTagSearch,
				'dataType' => 'json',
				'data'     => new \yii\web\JsExpression('function(term,page) { return {search:term}; }'),
				'results'  => new \yii\web\JsExpression('function(data,page) { return {results:data.results}; }'),
			],
			'createSearchChoice' => new \yii\web\JsExpression($newDataScript),
			'tags'               => true,
			'createTag'          => new \yii\web\JsExpression($newDataScript),
			'tokenSeparators'    => [',', ';'],
		],
	]);
	?>

	<?= $form->field($model, 'infoslide')->textarea(['rows' => 6]) ?>

	<div class="form-group">
		<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'),
			[
				'class' => $model->isNewRecord ? 'btn btn-success btn-block loading' : 'btn btn-primary',
			]) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
