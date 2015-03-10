<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\models\Result;
use kartik\widgets\SwitchInput;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ResultSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Results');
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = "Table View";
?>
<div class="result-index">

    <h1><?= Html::encode($this->title) ?> for Round #<?= $round_number ?></h1>
    <p class="text-right">
        <?=
        Html::checkbox("autoupdate", false, [
            'label' => "Auto Update <i id='pjax-status' class=''></i>",
            "data-pjax" => 0,
        ]);
        ?>
        &nbsp;|&nbsp;
        <?=
        Html::a("Switch to Venue View", ["round",
            "id" => $round_id,
            "tournament_id" => $tournament->id,
            "view" => "venue",
                ], ["class" => "btn btn-default"]);
        ?>
    </p>
    <!-- AJAX -->
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'id' => 'debates',
        'pjax' => true,
        'pjaxSettings' => [
            'loadingCssClass' => false,
        ],
        'striped' => false,
        'responsive' => true,
        'hover' => true,
        'floatHeader' => true,
        'floatHeaderOptions' => ['scrollingTop' => 100],
        'rowOptions' => function ($model, $key, $index, $grid) {
    return ["class" => ($model->result) ? "bg-success" : "bg-warning"];
},
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => 'kartik\grid\BooleanColumn',
                'attribute' => 'Entered',
                'vAlign' => 'middle',
                'value' => function ($model, $key, $index, $widget) {
                    return ($model->result instanceof Result) ? true : false;
                },
            ],
            [
                'class' => 'kartik\grid\DataColumn',
                'attribute' => "debate.venue",
                'format' => 'raw',
                'vAlign' => GridView::ALIGN_MIDDLE,
                'value' => function ($model, $key, $index, $widget) {
                    return $model->venue->name;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \common\models\search\VenueSearch::getSearchArray($tournament->id),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'Any Venue'],
            ],
            [
                'class' => 'kartik\grid\DataColumn',
                'attribute' => "result.og_place",
                'format' => 'raw',
                'vAlign' => GridView::ALIGN_MIDDLE,
                'value' => function ($model, $key, $index, $widget) {
                    return ($model->result) ? $model->result->og_place : "";
                },
            ],
            [
                'class' => 'kartik\grid\DataColumn',
                'attribute' => "result.oo_place",
                'format' => 'raw',
                'vAlign' => GridView::ALIGN_MIDDLE,
                'value' => function ($model, $key, $index, $widget) {
                    return ($model->result) ? $model->result->oo_place : "";
                },
            ],
            [
                'class' => 'kartik\grid\DataColumn',
                'attribute' => "result.cg_place",
                'format' => 'raw',
                'vAlign' => GridView::ALIGN_MIDDLE,
                'value' => function ($model, $key, $index, $widget) {
                    return ($model->result) ? $model->result->cg_place : "";
                },
            ],
            [
                'class' => 'kartik\grid\DataColumn',
                'attribute' => "result.co_place",
                'format' => 'raw',
                'vAlign' => GridView::ALIGN_MIDDLE,
                'value' => function ($model, $key, $index, $widget) {
                    return ($model->result) ? $model->result->co_place : "";
                },
            ],
            [
                'class' => 'kartik\grid\DataColumn',
                'attribute' => "result.time",
                'value' => function ($model, $key, $index, $widget) {
                    return ($model->result) ? $model->result->time : "";
                },
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'width' => "100px",
                'template' => '{view}&nbsp;&nbsp;{update}&nbsp;&nbsp;{delete}',
                'dropdown' => false,
                'vAlign' => 'middle',
                'urlCreator' => function($action, $model, $key, $index) {
                    if ($model->result instanceof Result)
                        return \yii\helpers\Url::to(["result/" . $action, "id" => $model->result->id, "tournament_id" => $model->tournament_id]);
                    else
                        return \yii\helpers\Url::to(["result/" . $action, "id" => $model->id, "tournament_id" => $model->tournament_id]);
                    ;
                },
                        'viewOptions' => ['label' => '<i class="glyphicon glyphicon-folder-open"></i>', 'title' => Yii::t("app", "View Result Details"), 'data-toggle' => 'tooltip'],
                        'updateOptions' => ['title' => Yii::t("app", "Correct Result"), 'data-toggle' => 'tooltip'],
                    ],
                ],
            ]);
            ?>
</div>
