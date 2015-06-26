<?php


use yii\widgets\DetailView;
use kartik\helpers\Html;
use \common\models\Team;
use common\models\Panel;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $tournament common\models\Tournament */
/* @var $model frontend\models\DebregsyncForm */

$this->title = "DebReg Sync";
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="debreg-sync">

	<?php $form = ActiveForm::begin(['id' => 'debregsync-form']); ?>

	<?= $form->field($model, 'url')->textInput(['maxlength' => 255]) ?>

	<?= $form->field($model, 'key')->textInput(['maxlength' => 255]) ?>

	<?= Html::hiddenInput("mode", "sync") ?>

	<div class="form-group">
		<?= Html::submitButton(\kartik\helpers\Html::icon("send") . "&nbsp" . Yii::t('app', 'Make it so!'), ['class' => 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
