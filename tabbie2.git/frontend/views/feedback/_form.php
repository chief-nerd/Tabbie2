<?php

use kartik\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Question;

/* @var $this yii\web\View */
/* @var $model common\models\feedback */
/* @var $form yii\widgets\ActiveForm */

$tournament = $this->context->_getContext();
?>

<div class="feedback-form">

	<?php $form = ActiveForm::begin(); ?>

	<div class="panel-group" id="feedback-accordion" role="tablist" aria-multiselectable="true">

		<?
		$i = 0;
		foreach ($model_group as $models): ?>
			<div class="panel panel-default">
				<div class="panel-heading" role="tab" id="headingOne">
					<h4 class="panel-title">
						<a role="button" data-toggle="collapse" data-parent="#feedback-accordion"
						   href="#collapse<?= $i ?>"
						   aria-expanded="true" aria-controls="collapseOne">
							<?= $models["title"] ?>
						</a>
					</h4>
				</div>
				<div id="collapse<?= $i ?>"
					 class="panel-collapse collapse <?= ($i == 0 && count($model_group) == 1) ? "in" : "" ?>"
					 role="tabpanel" aria-labelledby="headingOne">
					<div class="panel-body">
						<? foreach ($models["item"] as $q_id => $model): ?>
							<div class="form-group field-answer-value">
								<?= $model->renderLabel($i, $q_id) ?>
								<?= $model->renderField($i, $q_id) ?>
								<div class="help-block"></div>
							</div>
						<? endforeach; ?>
					</div>
				</div>
			</div>
			<?
			$i++;
		endforeach; ?>
	</div>

	<div class="form-group">
		<?= Html::submitButton(Html::icon("send") . "&nbsp;" . Yii::t('app', 'Submit Feedback'), ['class' => 'btn btn-success btn-block']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
