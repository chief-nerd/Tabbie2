<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Result */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="result-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'debate_id')->textInput() ?>

    <?= $form->field($model, 'og_speaks')->textInput() ?>

    <?= $form->field($model, 'og_place')->textInput() ?>

    <?= $form->field($model, 'oo_speaks')->textInput() ?>

    <?= $form->field($model, 'oo_place')->textInput() ?>

    <?= $form->field($model, 'cg_speaks')->textInput() ?>

    <?= $form->field($model, 'cg_place')->textInput() ?>

    <?= $form->field($model, 'co_speaks')->textInput() ?>

    <?= $form->field($model, 'co_place')->textInput() ?>

    <?= $form->field($model, 'time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
