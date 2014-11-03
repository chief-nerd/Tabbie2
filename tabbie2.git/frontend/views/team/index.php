<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use common\models\Team;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\TeamSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Teams');
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="team-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?=
        Html::a(Yii::t('app', 'Create {modelClass}', [
                    'modelClass' => 'Team',
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
            'pageSummary' => 'Page Total',
            'value' => function ($model, $key, $index, $widget) {
                return Html::a($model->name, ["view", "id" => $model->id, "tournament_id" => $model->tournament->id]);
            },
                    'format' => 'raw'
                ],
                [
                    'class' => '\kartik\grid\DataColumn',
                    'attribute' => 'speakerA.name',
                    'format' => 'raw',
                    'value' => function ($model, $key, $index, $widget) {
                        return Html::a($model->speakerA->name, ["user/view", "id" => $model->speakerA->id]);
                    }
                        ],
                        [
                            'class' => '\kartik\grid\DataColumn',
                            'attribute' => 'speakerB.name',
                            'format' => 'raw',
                            'value' => function ($model, $key, $index, $widget) {
                                return Html::a($model->speakerB->name, ["user/view", "id" => $model->speakerB->id]);
                            },
                                ],
                                [
                                    'class' => 'kartik\grid\ActionColumn',
                                    'template' => '{view}&nbsp;&nbsp;{update}&nbsp;&nbsp;{delete}',
                                    'dropdown' => false,
                                    'vAlign' => 'middle',
                                    'urlCreator' => function($action, $model, $key, $index) {
                                        return \yii\helpers\Url::to(["team/" . $action, "id" => $model->id, "tournament_id" => $model->tournament->id]);
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
                                                Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'Add Team'), ["team/create", "tournament_id" => $tournament->id], ['class' => 'btn btn-success'])
                                            ]
                                        ]
                                    ])
                                    ?>

</div>
