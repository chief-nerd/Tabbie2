<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\StarRating;

/* @var $this yii\web\View */
/* @var $model common\models\Adjudicator */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="adjudicator-form">

    <?php $form = ActiveForm::begin(); ?>

    <?
    $url = Url::to(['user/list']);

    // Script to initialize the selection based on the value of the select2 element
    $initScript = <<< SCRIPT
function (element, callback) {
    var id=\$(element).val();
    if (id !== "") {
        \$.ajax("{$url}?id=" + id, {
        dataType: "json"
        }).done(function(data) { callback(data.results);});
    }
}
SCRIPT;

    $Societyurl = Url::to(['user/societies']);

    // Script to initialize the selection based on the value of the select2 element
    $initSocietyScript = <<< SCRIPT
function (element, callback) {
    var id=\$(element).val();
    if (id !== "") {
        \$.ajax("{$Societyurl}?id=" + id, {
        dataType: "json"
        }).done(function(data) { callback(data.results);});
    }
}
SCRIPT;

    echo $form->field($model, 'user_id')->widget(Select2::classname(), [
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
                'url' => $url,
                'dataType' => 'json',
                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
            ],
            'initSelection' => new JsExpression($initScript)
        ],
    ]);
    ?>
    <?
    echo $form->field($model, 'society_id')->widget(Select2::classname(), [
        'options' => [
            'placeholder' => 'Search for a societies ...',
            'multiple' => true,
        ],
        'addon' => [
            "prepend" => [
                "content" => '<i class="glyphicon glyphicon-tower"></i>'
            ],
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 3,
            'ajax' => [
                'url' => $Societyurl,
                'dataType' => 'json',
                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
            ],
            'initSelection' => new JsExpression($initSocietyScript)
        ],
    ]);
    ?>

    <?=
    $form->field($model, 'strength')->widget(StarRating::className(), [
        "pluginOptions" => [
            "stars" => 8,
            "min" => 0,
            "max" => 9,
            "step" => 1,
            "size" => "md",
            "starCaptions" => [
                "1" => 'Bad Judge',
                "2" => 'Can Judge',
                "3" => 'Decent Judge',
                "4" => 'Average Judge',
                "5" => 'High Potential',
                "6" => 'Chair',
                "7" => 'Good Chair',
                "8" => 'EUDC Break',
                "9" => 'Judge God',
            ]
        ],
    ])
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
