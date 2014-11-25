<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Result */
/* @var $form yii\widgets\ActiveForm */
$this->title = "Confirm Data";
?>

<div class="result-confirm">

    <?php $form = ActiveForm::begin(); ?>

    <?= Html::activeHiddenInput($model, 'debate_id') ?>
    <?
    /* @var $debate Debate */
    $debate = $model->debate;
    $cols = "col-xs-12 col-sm-6";
    $fieldOption = [
        "template" => "{label} {input}\n{hint}\n{error}",
    ];
    $textOption = ["size" => 1, "maxlength" => 1, "readonly" => "readonly"];
    ?>

    <div class="row">
        <div class="<?= $cols ?>">
            <h3>Opening Government</h3>
            <?= Html::activeHiddenInput($model, 'og_A_speaks'); ?>
            <?= Html::activeHiddenInput($model, 'og_B_speaks'); ?>
            <?= $form->field($model, 'og_place', $fieldOption)->label($debate->og_team->name)->textInput($textOption) ?>
        </div>
        <div class="<?= $cols ?>">
            <h3>Opening Opposition</h3>
            <?= Html::activeHiddenInput($model, 'oo_A_speaks'); ?>
            <?= Html::activeHiddenInput($model, 'oo_B_speaks'); ?>
            <?= $form->field($model, 'oo_place', $fieldOption)->label($debate->oo_team->name)->textInput($textOption) ?>
        </div>
    </div>
    <div class="row">
        <div class="<?= $cols ?>">
            <h3>Closing Government</h3>
            <?= Html::activeHiddenInput($model, 'cg_A_speaks'); ?>
            <?= Html::activeHiddenInput($model, 'cg_B_speaks'); ?>
            <?= $form->field($model, 'cg_place', $fieldOption)->label($debate->cg_team->name)->textInput($textOption) ?>
        </div>
        <div class="<?= $cols ?>">
            <h3>Closing Opposition</h3>
            <?= Html::activeHiddenInput($model, 'co_A_speaks'); ?>
            <?= Html::activeHiddenInput($model, 'co_B_speaks'); ?>
            <?= $form->field($model, 'co_place', $fieldOption)->label($debate->co_team->name)->textInput($textOption) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::activeHiddenInput($model, "confirmed", ["value" => "true"]); ?>
        <?= Html::submitButton(Yii::t('app', 'Make it so!'), ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app', 'Nope, go back'), ["create", "id" => $debate->id, "tournament_id" => $debate->tournament_id], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
