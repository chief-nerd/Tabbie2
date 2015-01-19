<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Question;

/* @var $this yii\web\View */
/* @var $model common\models\feedback */
/* @var $form yii\widgets\ActiveForm */

$tournament = $this->context->_getContext();
?>

<div class="feedback-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= Html::activeHiddenInput($model, 'debate_id') ?>

    <?
    /* @var $q Question */
    foreach ($model->getQuestions($tournament) as $q):
        ?>
        <div class="row">
            <div class="col-xs-12">
                <?= $q->renderLabel() ?>
            </div>
            <div class="col-xs-12">
                <?= $q->renderInput() ?>
            </div>
        </div>
    <? endforeach; ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
