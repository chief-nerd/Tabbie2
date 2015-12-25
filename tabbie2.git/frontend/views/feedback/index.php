<?php

use common\models\search\VenueSearch;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\FeedbackSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Feedbacks');
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="feedback-index">

    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-xs-12 col-sm-6">
            <p class="text-right">
                <?= Html::a("Feedback Export", ["export", "tournament_id" => $tournament->id], ["class" => "btn btn-default"]) ?>
            </p>
        </div>
    </div>

    <?
    $gridColumns = [
        [
            'class' => '\kartik\grid\SerialColumn'
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'round_number',
            'format' => 'raw',
            'value' => function ($model, $key, $index, $widget) {
                return $model->debate->round->number;
            },
            'filter' => \common\models\search\TournamentSearch::getRoundOptions($tournament->id),
            'filterType' => GridView::FILTER_SELECT2,
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => Yii::t("app", 'Any {object} ...', ['object' => Yii::t("app", 'Round')])],
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'venue_name',
            'format' => 'raw',
            'value' => function ($model, $key, $index, $widget) {
                return $model->debate->venue->name;
            },
            'filter' => VenueSearch::getSearchArray($tournament->id),
            'filterType' => GridView::FILTER_SELECT2,
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => Yii::t("app", 'Any {object} ...', ['object' => Yii::t("app", 'Venue')])],
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'from',
            'format' => 'raw',
            'value' => function ($model, $key, $index, $widget) {
                return $model->from->name;
            },
            'filter' => \common\models\search\UserSearch::getSearchTournamentArray($tournament->id, true),
            'filterType' => GridView::FILTER_SELECT2,
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => Yii::t("app", 'Any {object} ...', ['object' => Yii::t("app", 'User')])],
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'to',
            'format' => 'raw',
            'value' => function ($model, $key, $index, $widget) {
                return $model->to->name;
            },
            'filter' => \common\models\search\UserSearch::getSearchTournamentArray($tournament->id, true),
            'filterType' => GridView::FILTER_SELECT2,
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => Yii::t("app", 'Any {object} ...', ['object' => Yii::t("app", 'User')])],
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'time',
        ],
        [
            'class' => 'kartik\grid\ActionColumn',
            'width' => "100px",
            'template' => '{view}',
            'dropdown' => false,
            'vAlign' => 'middle',
            'urlCreator' => function ($action, $model, $key, $index) {
                return \yii\helpers\Url::to(["feedback/" . $action, "id" => $model->id, "tournament_id" => $model->debate->tournament_id]);
            },
            'viewOptions' => ['label' => '<i class="glyphicon glyphicon-folder-open"></i>', 'title' => Yii::t("app", 'View {modelClass}', ['modelClass' => 'Feedback']), 'data-toggle' => 'tooltip'],
            'updateOptions' => ['title' => Yii::t("app", 'Update {modelClass}', ['modelClass' => 'Feedback']), 'data-toggle' => 'tooltip'],
        ]
    ];

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $gridColumns,
        'id' => 'feedback',
        'pjax' => true,
        'showPageSummary' => false,
        'responsive' => true,
        'hover' => true,
        'floatHeader' => false,
        'floatHeaderOptions' => ['scrollingTop' => '150'],
    ])
    ?>

</div>
