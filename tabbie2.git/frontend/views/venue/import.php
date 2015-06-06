<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Team */

$this->title = Yii::t('app', 'Import {modelClass}', [
	'modelClass' => 'Venue',
]);
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Venue'), 'url' => ['index', 'tournament_id' => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
	<div class="team-import">

	<h1><?= Html::encode($this->title) ?></h1>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

<? if (isset($model->tempImport)): ?>

	<? for ($i = 1; $i <= count($model->tempImport); $i++): ?>
		<div class="row">

			<div class="col-sm-2 green">
				<?= $model->tempImport[$i][0] ?>
			</div>

			<div class="col-sm-2 green">
				<?= $model->tempImport[$i][1] ?>
			</div>

			<div class="col-sm-2 green">
				<?= $model->tempImport[$i][2] ?>
			</div>
		</div>
	<? endfor; ?>
	<div class="form-group">
		<?= Html::hiddenInput("csvFile", serialize($model->tempImport)); ?>
		<?= Html::hiddenInput("makeItSo", "true"); ?>
		<?= Html::submitButton(Yii::t('app', 'Make it so'), ['class' => 'btn btn-success btn-block loading'])
		?>
	</div>

<? else: ?>
	<div class="venue-form">
		<?=
		$form->field($model, 'csvFile')->fileInput([
			'accept' => '.csv'
		])
		?>

		<div class="form-group">
			<?= Html::submitButton(Yii::t('app', 'Import'), ['class' => 'btn btn-success']) ?>
		</div>


	</div>
<? endif; ?>

<?php ActiveForm::end(); ?>