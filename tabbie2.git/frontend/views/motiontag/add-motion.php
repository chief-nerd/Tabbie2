<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\LegacyMotion */

$this->title = Yii::t('app', 'Add Motion');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Motion Tags'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="legacy-motion-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<div class="legacy-motion-form">

		<?php $form = \kartik\widgets\ActiveForm::begin(); ?>

		<?= $form->field($model, 'tournament')->textInput() ?>

		<?= $form->field($model, 'round')->textInput([
			'options' => ['placeholder' => Yii::t("app", 'Round #1')],
		]) ?>

		<?= $form->field($model, 'link')->textInput(['maxlength' => true]) ?>

		<?= $form->field($model, 'time', [
			'addon' => ['prepend' => ['content' => "<i class=\"glyphicon glyphicon-calendar\"></i>"]]
		])->widget(\kartik\widgets\DatePicker::classname(), [
			'type'          => \kartik\date\DatePicker::TYPE_INPUT,
			'options'       => ['placeholder' => Yii::t("app", 'Enter date ...')],
			'pluginOptions' => [
				'format'    => 'yyyy-mm-dd',
				'autoclose' => true,
			]
		]);
		?>

		<?= $form->field($model, 'motion')->textInput() ?>

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
			<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
		</div>

		<?php \kartik\widgets\ActiveForm::end(); ?>

	</div>

</div>
