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

	<? foreach ($models as $q_id => $model):
		?>
		<div class="form-group field-answer-value">
			<?= $model->renderLabel($q_id) ?>
			<?= $model->renderField($q_id) ?>
			<div class="help-block"></div>
		</div>
	<? endforeach; ?>

	<div class="form-group">
		<?= Html::submitButton(Html::icon("send") . "&nbsp;" . Yii::t('app', 'Submit Feedback'), ['class' => 'btn btn-success btn-block']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
