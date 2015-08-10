<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\Round */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="round-form">

	<?php $form = ActiveForm::begin(); ?>

	<?
	$urlVenueList = \yii\helpers\Url::to(['venue/list', 'tournament_id' => $model->tournament_id]);
	$urlAdjudicatorList = \yii\helpers\Url::to(['adjudicator/list', 'tournament_id' => $model->tournament_id]);
	$urlTeamList = \yii\helpers\Url::to(['team/list', 'tournament_id' => $model->tournament_id]);

	$newDataScript = <<< SCRIPT
function (query) {
    return {
      id: query.term,
      text: query.term,
      tag: true
    }
  }
SCRIPT;
	?>

	<?= Html::activeHiddenInput($model, 'label') ?>

	<?= Html::activeHiddenInput($model, 'tournament_id') ?>


	<?= $form->field($model, 'type')->dropDownList($model->getTypeOptions()); ?>

	<?= $form->field($model, 'level')->dropDownList($model->getLevelOptions()); ?>

	<?= $form->field($model, 'motion')->textarea(['rows' => 2]) ?>

	<?= $form->field($model, 'infoslide')->textarea(['rows' => 6]) ?>

	<div class="form-group">
		<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Next Step') : Yii::t('app', 'Update'),
			[
				'class' => 'btn btn-success btn-block',
			]) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
