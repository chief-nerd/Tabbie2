<?php

use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

$this->title = Yii::t("app", 'Signup');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
	<h1><?= Html::encode($this->title) ?></h1>

	<p><?= Yii::t("app", "Please fill out the following fields to signup:") ?></p>

	<div class="signup form-group">
		<?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

		<div class="row">
			<div class="col-lg-12">
				<?= $form->field($model, 'email') ?>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6">
				<?= $form->field($model, 'password')->passwordInput() ?>
			</div>
			<div class="col-lg-6">
				<?= $form->field($model, 'password_repeat')->passwordInput() ?>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6">
				<?= $form->field($model, 'givenname')->textInput() ?>
			</div>
			<div class="col-lg-6">
				<?= $form->field($model, 'surename')->textInput() ?>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6">
				<div class="help_popup" title="Why do we ask for this?">
					<?= Yii::t("app", "Most tournament allocation algorithm in this system try also to take panel diversity into account.
					For this to work at all, we would politely ask to choose an option from this list.
					We are aware that not every personal preference can be matched by our choises and apologise for missing options.
					If you feel that none of the options is in any applicable please choose <Not Revealing>.
					This option will never be shown to any user and is only for calculation purposes only!");
					?>
				</div>
				<?= $form->field($model, 'gender')->dropDownList(\common\models\User::genderOptions()) ?>
			</div>
			<div class="col-lg-6">
				<?
				$urlUserList = Url::to(['society/list']);

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
				$newDataScript = <<< SCRIPT
function (term, data) {
	if ($(data).length === 0) {
			return {
				id: term,
                text: term
            };
        }
        }
SCRIPT;
				echo $form->field($model, 'societies_id')->widget(Select2::classname(), [
					'options' => [
						'placeholder' => Yii::t("app", 'Search for a society ...'),
						'multiple' => false,
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
						'initSelection' => new JsExpression($initUserScript),
						'createSearchChoice' => new JsExpression($newDataScript)
					],
				]);
				?>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-2">
				<?= $model->getPictureImage(150, 150, ["id" => "previewImageUpload"]) ?>
			</div>
			<div class="col-sm-10">

				<?= $form->field($model, 'picture')->fileInput() ?>

				<script>
					var s = document.getElementById('signupform-picture');
					s.onchange = function (event) {
						document.getElementById('previewImageUpload').src = URL.createObjectURL(event.target.files[0]);
					}
				</script>
			</div>
		</div>

		<div class="form-group">
			<?= Html::submitButton(Yii::t("app", 'Signup'), ['class' => 'btn btn-success btn-block', 'name' => 'signup-button']) ?>
		</div>
		<?php ActiveForm::end(); ?>
	</div>
</div>
