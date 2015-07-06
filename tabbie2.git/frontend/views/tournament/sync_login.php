<?php


use yii\widgets\DetailView;
use kartik\helpers\Html;
use \common\models\Team;
use common\models\Panel;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $tournament common\models\Tournament */
/* @var $model frontend\models\DebregsyncForm */

$this->title = "DebReg Sync";
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="debreg-sync">

	<?php $form = ActiveForm::begin(['id' => 'debregsync-form']); ?>

	<h1>Login to DebReg</h1>
	<?= $form->field($model, 'username')->textInput(['maxlength' => 255]) ?>

	<?= $form->field($model, 'password')->passwordInput(['maxlength' => 255]) ?>

	<?= $form->field($model, 'debregId')->label(Yii::t("app", "DebReg Tournament"))->textInput() ?>

	<? $url = \yii\helpers\Url::to(["checkin/select-tournaments"]) ?>
	<? /*$form->field($model, 'debregId')->label(Yii::t("app", "DebReg Tournament"))->widget(Select2::classname(), [
			'options' => ['placeholder' => Yii::t("app", 'Tournament name ...')],
			'pluginOptions' => [
				'allowClear' => true,
				'minimumInputLength' => 2,
				'ajax' => [
					'url' => $url,
					'dataType' => 'json',
					'data' => new JsExpression('function(term,page) { return {search:term}; }'),
					'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
				],
			],
		]
	); */ ?>


	<?= Html::hiddenInput("mode", "sync") ?>

	<div class="form-group">
		<?= Html::submitButton(\kartik\helpers\Html::icon("send") . "&nbsp" . Yii::t('app', 'Make it so!'), ['class' => 'btn btn-primary loading']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
