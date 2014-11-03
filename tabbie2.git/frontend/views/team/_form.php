<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model common\models\Team */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="team-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?
    $urlUserList = Url::to(['user/list']);

    // Script to initialize the selection based on the value of the select2 element
    $initUserScript = <<< SCRIPT
function (element, callback) {
    var id=\$(element).val();
    if (id !== "") {
        \$.ajax("{$urlUserList}?id=" + id, {
        dataType: "json"
        }).done(function(data) { callback(data.results);});
    }
}
SCRIPT;

    echo $form->field($model, 'speakerA_id')->widget(Select2::classname(), [
        'options' => ['placeholder' => 'Search for a user ...'],
        'addon' => [
            "prepend" => [
                "content" => '<i class="glyphicon glyphicon-user"></i>'
            ],
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 3,
            'ajax' => [
                'url' => $urlUserList,
                'dataType' => 'json',
                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
            ],
            'initSelection' => new JsExpression($initUserScript)
        ],
    ]);
    ?>

    <?=
    $form->field($model, 'speakerB_id')->widget(Select2::classname(), [
        'options' => ['placeholder' => 'Search for a user ...'],
        'addon' => [
            "prepend" => [
                "content" => '<i class="glyphicon glyphicon-user"></i>'
            ],
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 3,
            'ajax' => [
                'url' => $urlUserList,
                'dataType' => 'json',
                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
            ],
            'initSelection' => new JsExpression($initUserScript)
        ],
    ]);
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
