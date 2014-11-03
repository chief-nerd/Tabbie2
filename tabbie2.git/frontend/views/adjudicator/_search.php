<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\AdjudicatorSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="adjudicator-search">

    <?php
    $form = ActiveForm::begin([
                'action' => ['index', 'tournament_id' => $model->tournament_id],
                'method' => 'get',
    ]);
    ?>

    <div class="row">
        <div class="col-xs-1"><?= $form->field($model, 'id') ?></div>
        <div class="col-xs-4"><?= $form->field($model, 'judge_name') ?></div>
        <div class="col-xs-4"><?= $form->field($model, 'strength') ?></div>
        <div class="form-group col-xs-3 text-right">
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
