<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TabAfterRound */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="draw-after-round-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'tournament_id')->textInput() ?>

    <?= $form->field($model, 'round_id')->textInput() ?>

    <?= $form->field($model, 'time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
