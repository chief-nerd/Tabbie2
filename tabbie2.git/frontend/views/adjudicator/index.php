<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use common\models\Adjudicator;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\AdjudicatorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Adjudicators');
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="adjudicator-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?=
        Html::a(Yii::t('app', 'Create {modelClass}', [
                    'modelClass' => 'Adjudicator',
                ]), ['create', "tournament_id" => $tournament->id], ['class' => 'btn btn-success'])
        ?>
    </p>
    <?
    $gridColumns = [
        [
            'class' => '\kartik\grid\SerialColumn',
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'name',
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => \common\models\search\AdjudicatorSearch::getSearchArray($tournament->id),
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => 'Any Adjudicator'],
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'societyName',
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => \common\models\search\SocietySearch::getSearchArray($tournament->id),
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => 'Any Society'],
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'strength',
            'format' => "raw",
            'value' => function ($model, $key, $index, $widget) {
                return "( $model->strength )&nbsp;&nbsp;" . Adjudicator::translateStrength($model->strength);
            },
            'width' => '15%',
        ],
        [
            'class' => 'kartik\grid\ActionColumn',
            'template' => '{view}&nbsp;&nbsp;{update}&nbsp;&nbsp;{delete}',
            'dropdown' => false,
            'vAlign' => 'middle',
            'urlCreator' => function($action, $model, $key, $index) {
                return \yii\helpers\Url::to(["adjudicator/" . $action, "id" => $model->id, "tournament_id" => $model->tournament->id]);
            },
                    'viewOptions' => ['label' => '<i class="glyphicon glyphicon-folder-open"></i>', 'title' => Yii::t("app", "View Venue"), 'data-toggle' => 'tooltip'],
                    'updateOptions' => ['title' => Yii::t("app", "Update team"), 'data-toggle' => 'tooltip'],
                    'deleteOptions' => ['title' => Yii::t("app", "Delete team"), 'data-toggle' => 'tooltip'],
                ],
            ];

            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => $gridColumns,
                'showPageSummary' => false,
                'responsive' => true,
                'hover' => true,
                'floatHeader' => false,
                'floatHeaderOptions' => ['scrollingTop' => '150'],
                'toolbar' => [
                    ['content' =>
                        Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'Add Adjudicator'), ["adjudicator/create", "tournament_id" => $tournament->id], ['class' => 'btn btn-success'])
                    ]
                ]
            ])
            ?>

</div>
