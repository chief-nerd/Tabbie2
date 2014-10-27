<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model common\models\Tournament */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tournament-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= Html::activeHiddenInput($model, 'convenor_user_id', ["value" => Yii::$app->user->id]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 100, 'placeholder' => 'My super awesome IV']) ?>

    <?= $form->field($model, 'tabmaster_user_id')->dropDownList($model->getTabmasterOptions()) ?>

    <?=
    $form->field($model, 'start_date')->widget(DateTimePicker::classname(), [
        'type' => DateTimePicker::TYPE_INPUT,
        'options' => ['placeholder' => 'Enter start date / time ...'],
        'pluginOptions' => [
            'autoclose' => true
        ]
    ]);
    ?>

    <?=
    $form->field($model, 'end_date')->widget(DateTimePicker::classname(), [
        'type' => DateTimePicker::TYPE_INPUT,
        'options' => ['placeholder' => 'Enter the end date / time ...'],
        'pluginOptions' => [
            'autoclose' => true
        ]
    ]);
    ?>

    <div class="row">
        <div class="col-sm-2">
            <img class="image-responsive" style="margin-right: 50px;" src="<?= $model["logo"] ?>" height="150" alt="<?= $model["fullname"] ?>">
        </div>
        <div class="col-sm-10">
            <?= $form->field($model, 'logo')->fileInput() ?>
        </div>
    </div>
    <br>
    <div class="form-group row">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
