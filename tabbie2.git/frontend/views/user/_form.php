<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \common\models\User;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>
    <?=
    $form->field($model, 'status')->dropDownList(User::getStatusOptions());
    ?>
    <?= $form->field($model, 'username')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'givenname')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'surename')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => 255]) ?>

    <?=
    $form->field($model, 'role')->dropDownList(User::getRoleOptions());
    ?>

    <?= $form->field($model, 'picture')->fileInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
