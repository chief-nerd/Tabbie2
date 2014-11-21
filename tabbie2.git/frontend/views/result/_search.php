<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\ResultSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="result-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'debate_id') ?>

    <?= $form->field($model, 'og_speaks') ?>

    <?= $form->field($model, 'og_place') ?>

    <?= $form->field($model, 'oo_speaks') ?>

    <?php // echo $form->field($model, 'oo_place') ?>

    <?php // echo $form->field($model, 'cg_speaks') ?>

    <?php // echo $form->field($model, 'cg_place') ?>

    <?php // echo $form->field($model, 'co_speaks') ?>

    <?php // echo $form->field($model, 'co_place') ?>

    <?php // echo $form->field($model, 'time') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
