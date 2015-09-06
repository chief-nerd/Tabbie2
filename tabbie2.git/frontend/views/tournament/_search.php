<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\TournamentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tournament-search">
	<?php
	$form = ActiveForm::begin([
		'action' => ["tournament/archive"],
		'method' => 'get',
	]);
	?>
	<div class="row">
		<div class="col-xs-8"><?= $form->field($model, 'name') ?></div>
		<div class="form-group col-xs-4 text-right btn-group buttons">
			<?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
			<?= Html::a(Yii::t('app', 'Reset'), ["tournament/index"], ['class' => 'btn btn-default']) ?>
		</div>
	</div>

	<?php ActiveForm::end(); ?>

</div>
