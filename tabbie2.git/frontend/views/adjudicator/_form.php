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

    echo $form->field($model, 'user_id')->widget(Select2::classname(), [
        'options' => ['placeholder' => 'Search for a user ...'],
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

    <?=
    $form->field($model, 'strength')->widget(StarRating::className(), [
        "pluginOptions" => [
            "stars" => 5,
            "min" => 0,
            "max" => 5,
            "step" => 0.5,
            "size" => "md",
            "starCaptions" => [
                "0.5" => 'Very Poor Judge',
                "1" => 'Bad Judge',
                "1.5" => 'Can Judge',
                "2" => 'Decent Judge',
                "2.5" => 'Average Judge',
                "3" => 'High Potential',
                "3.5" => 'Chair',
                "4" => 'Good Chair',
                "4.5" => 'EUDC Break',
                "5" => 'EUDC Final Chair'
            ]
        ],
    ])
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
