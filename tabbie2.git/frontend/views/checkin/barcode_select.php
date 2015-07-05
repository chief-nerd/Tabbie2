<?php
/**
 * barcode_select.php File
 *
 * @package  Tabbie2
 * @author   jareiter
 * @version
 */
use kartik\widgets\ActiveForm;
use kartik\helpers\Html;

?>
<div id="barcodeForm">
	<?php $form = ActiveForm::begin(); ?>

	<div class="row">
		<?= Html::input("userID") ?>
	</div>
	<div class="row">
		Vertical Offset: <?= Html::dropDownList("pos_v", '', ['none', 1, 2, 3, 4, 5, 6, 7, 8, 9]) ?><br>
		Horizontal Offset: <?= Html::dropDownList("pos_h", '', ['none', 1, 2, 3, 4, 5, 6, 7, 8, 9]) ?>
	</div>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('app', 'Print Barcodes'),
			[
				'class' => 'btn btn-success',
			]) ?>
	</div>
	<?php ActiveForm::end(); ?>
</div>