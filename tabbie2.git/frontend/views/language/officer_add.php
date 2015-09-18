<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\widgets\Select2;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model common\models\LanguageOfficer */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('app', 'Add {object}', [
	'object' => Yii::t("app", 'Language Officer'),
]);

$this->params['breadcrumbs'][] = ['label' => $model->tournament->fullname, 'url' => ['tournament/view', "id" => $model->tournament->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Language Officer'), 'url' => ['language/officer', "tournament_id" => $model->tournament->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Add');
?>

<div class="language-officer-add">

	<h1><?= Html::encode($this->title) ?></h1>

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

		echo $form->field($model, 'user_id')->widget(Select2::classname(), [
			'options'       => ['placeholder' => Yii::t("app", 'Search for a User ...')],
			'addon'         => [
				"prepend" => [
					"content" => '<i class="glyphicon glyphicon-user"></i>'
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

		<div class="form-group">
			<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Add') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
		</div>

		<?php ActiveForm::end(); ?>

	</div>

</div>