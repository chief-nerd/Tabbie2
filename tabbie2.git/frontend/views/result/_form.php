<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Result */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="result-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= Html::activeHiddenInput($model, 'debate_id') ?>
    <?
    /* @var $debate Debate */
    $debate = $model->debate;
    $cols = "col-xs-12 col-sm-6";
    $fieldOption = [
        "template" => "{label} {input}\n{hint}\n{error}",
    ];
    $textOption = ["size" => 2, "maxlength" => 2];
    ?>

    <div class="row">
        <div class="<?= $cols ?>">
            <h3>Opening Government</h3>
            <h4><?= $debate->og_team->name ?></h4>
            <?= $form->field($model, 'og_A_speaks', $fieldOption)->label($debate->og_team->speakerA->name)->textInput($textOption) ?>
            <?= $form->field($model, 'og_B_speaks', $fieldOption)->label($debate->og_team->speakerB->name)->textInput($textOption) ?>
        </div>
        <div class="<?= $cols ?>">
            <h3>Opening Opposition</h3>
            <h4><?= $debate->oo_team->name ?></h4>
            <?= $form->field($model, 'oo_A_speaks', $fieldOption)->label($debate->oo_team->speakerA->name)->textInput($textOption) ?>
            <?= $form->field($model, 'oo_B_speaks', $fieldOption)->label($debate->oo_team->speakerB->name)->textInput($textOption) ?>
        </div>
    </div>
    <div class="row">
        <div class="<?= $cols ?>">
            <h3>Closing Government</h3>
            <h4><?= $debate->cg_team->name ?></h4>
            <?= $form->field($model, 'cg_A_speaks', $fieldOption)->label($debate->cg_team->speakerA->name)->textInput($textOption) ?>
            <?= $form->field($model, 'cg_B_speaks', $fieldOption)->label($debate->cg_team->speakerB->name)->textInput($textOption) ?>
        </div>
        <div class="<?= $cols ?>">
            <h3>Closing Opposition</h3>
            <h4><?= $debate->co_team->name ?></h4>
            <?= $form->field($model, 'co_A_speaks', $fieldOption)->label($debate->co_team->speakerA->name)->textInput($textOption) ?>
            <?= $form->field($model, 'co_B_speaks', $fieldOption)->label($debate->co_team->speakerB->name)->textInput($textOption) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
