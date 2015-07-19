<?php
	/**
	 * badge_select.php File
	 *
	 * @package  Tabbie2
	 * @author   jareiter
	 * @version
	 */
	use kartik\widgets\ActiveForm;
	use kartik\helpers\Html;

	$this->title = Yii::t('app', 'Generate Badges');
	/** @var \common\models\Tournament $tournament */
	$tournament = $this->context->_getContext();
	$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div id="barcodeForm">
	<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

	<? if ($tournament->badge): ?>
		<div class="row">
			<div class="col-xs-12">
				<img src="<?= $tournament->getBadge() ?>" width="300px">
			</div>
		</div>
	<? endif; ?>

	<div class="row">
		<div class="col-xs-2">
			<?= Html::label("Badge Background:", "badge"); ?>
		</div>
		<div class="col-xs-10">
			<?= Html::fileInput("badge", '') ?>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-2">
			<?= Html::label("Use Background:", "use"); ?>
		</div>
		<div class="col-xs-10">
			<?= Html::checkbox("use", true) ?>
		</div>
	</div>
	<br>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('app', 'Print Badges'),
			[
				'class' => 'btn btn-success',
			]) ?>
	</div>
	<?php ActiveForm::end(); ?>
</div>
