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

		<?= $form->field($model, 'tournament')->textInput([
			"placeholder" => Yii::t("app", "Your amazing IV")
		]) ?>

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

		<?= $form->field($model, 'round')->textInput([
			'placeholder' => Yii::t("app", 'Round #1 or Final'),
		]) ?>

		<?= $form->field($model, 'motion')->textInput([
			"placeholder" => Yii::t("app", "THW ...")
		]) ?>

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

		<?= $form->field($model, 'link')->textInput([
			"placeholder" => Yii::t("app", "http://give.credit.where.credit.is.due.com")
		]) ?>

		<div class="form-group">
			<?= Html::submitButton(Yii::t('app', 'Add this motion'), ['class' => 'btn btn-success btn-block']) ?>
		</div>

		<?php \kartik\widgets\ActiveForm::end(); ?>

	</div>

</div>
