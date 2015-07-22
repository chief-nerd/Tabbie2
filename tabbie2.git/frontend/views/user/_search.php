<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\UserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-search">

	<?php
	$form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
		'id' => 'user-index-form'
	]);
	?>

	<div class="row">
		<div class="col-xs-1"><?= $form->field($model, 'id') ?></div>
		<div class="col-xs-3"><?= $form->field($model, 'name') ?></div>
		<div class="col-xs-3"><?= $form->field($model, 'email') ?></div>
		<div class="col-xs-1"><?= $form->field($model, 'role')->label("Role")
				->dropDownList(\common\models\User::getRoleOptions(true)) ?></div>
		<div class="col-xs-3">
			<?= Html::label("Tournament", 'tournament') ?>
			<?
			$urlList = \yii\helpers\Url::to(["tournament/list"]);
			$id = Yii::$app->request->get("tournament", 'null');

			$initScript = <<< SCRIPT
function (element, callback) {
    var id=\$(element).val();
    if (id !== "") {
        \$.ajax("{$urlList}?tid=" + {$id}, {
        dataType: "json"
        }).done(function(data) { callback(data.results);});
    }
}
SCRIPT;
			?>
			<?= \kartik\widgets\Select2::widget([
				'name'          => 'tournament',
				'options'       => ['placeholder' => Yii::t("app", 'Search for a tournament ...')],
				'pluginOptions' => [
					'allowClear'         => true,
					'minimumInputLength' => 2,
					'ajax'               => [
						'url'      => $urlList,
						'dataType' => 'json',
						'data'     => new \yii\web\JsExpression('function(term,page) { return {search:term}; }'),
						'results'  => new \yii\web\JsExpression('function(data,page) { return {results:data.results}; }'),
					],
					'initSelection' => new \yii\web\JsExpression($initScript),
				],
				"pluginEvents"  => [
					"change" => "function() { document.getElementById('user-index-form').submit(); }",
				]
			]);
			?>
		</div>
		<div class="form-group col-xs-1 text-right">
			<?= Html::label("Action") ?>
			<?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
		</div>
	</div>

	<?php $form->end(); ?>

</div>
