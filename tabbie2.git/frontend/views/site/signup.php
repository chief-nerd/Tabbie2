<?php

use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

$this->title = 'Signup';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
	<h1><?= Html::encode($this->title) ?></h1>

	<p>Please fill out the following fields to signup:</p>

	<div class="row">
		<div class="col-lg-5">
			<?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>
			<?= $form->field($model, 'username') ?>
			<?= $form->field($model, 'surename') ?>
			<?= $form->field($model, 'givenname') ?>
			<?= $form->field($model, 'email') ?>
			<?= $form->field($model, 'password')->passwordInput() ?>
			<?
			$urlUserList = Url::to(['site/list-societies']);

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

			echo $form->field($model, 'societies_id')->widget(Select2::classname(), [
				'options' => [
					'placeholder' => 'Search for a societies ...',
					'multiple' => true,
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
					'initSelection' => new JsExpression($initUserScript)
				],
			]);
			?>
			<div class="form-group">
				<?= Html::submitButton('Signup', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
			</div>
			<?php ActiveForm::end(); ?>
		</div>
	</div>
</div>
