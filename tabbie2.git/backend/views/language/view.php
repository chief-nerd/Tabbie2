<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use \kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Language */

$this->title = $model->label;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Languages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$js = <<<JS
$(document).on('click', '.kv-editable-link', function() {
    $(this).parent().find("textarea").focus();
});
JS;

$this->registerJs($js);
?>
<div class="language-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="language-search">
        <?php
        $form = ActiveForm::begin([
                'action' => ["language/view", "id" => $model->language],
                'method' => 'get',
        ]);
        ?>
        <div class="row">
            <div class="col-xs-8"><?= $form->field($searchModel, 'translation')->label(Yii::t("app", "Search")) ?></div>
            <div class="form-group col-xs-4 text-right btn-group buttons">
                <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
                <?= Html::a(Yii::t('app', 'Reset'), ["language/view", "id" => $model->language], ['class' => 'btn btn-default']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

    <?= \kartik\grid\GridView::widget([
            'dataProvider' => $dataProvider,
            'columns'      => [['class' => 'yii\grid\SerialColumn'],
                    'original.category',
                    [
                            'class'     => \kartik\grid\DataColumn::className(),
                            'attribute' => 'original.message',
                            'format'    => 'text',
                            'label'     => 'Original',
                            'width'     => '40%',
                    ],
                    [
                            'class'           => kartik\grid\EditableColumn::className(),
                            'attribute'       => 'translation',
                            'editableOptions' => function ($model, $key, $index, $widget) {
                                return [
                                        'header'       => 'Translation',
                                        'size'         => 'md',
                                        'placement'    => \kartik\popover\PopoverX::ALIGN_TOP,
                                        'inputType'    => \kartik\editable\Editable::INPUT_TEXTAREA,

                                        'pluginEvents' => [
                                                "load.complete.popoverX" => "function(event, val) { log('Go'); }",
                                        ],
                                ];
                            }
                    ],
                //['class' => 'yii\grid\ActionColumn'],
            ],
    ]); ?>

</div>
