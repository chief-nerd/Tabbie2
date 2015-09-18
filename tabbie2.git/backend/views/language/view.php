<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Language */

$this->title = $model->label;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Languages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="language-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= \kartik\grid\GridView::widget(['dataProvider' => $dataProvider,
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
