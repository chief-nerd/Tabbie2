<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use common\models\Tournament;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\TeamSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Teams');
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="team-index">
    <?
    $gridColumns = [
        [
            'class' => '\kartik\grid\SerialColumn',
        ],
        [
            'class' => 'kartik\grid\BooleanColumn',
            'attribute' => 'active',
            'vAlign' => 'middle',
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'name',
            'format' => 'raw',
            'vAlign' => GridView::ALIGN_MIDDLE,
            'value' => function ($model, $key, $index, $widget) {
                return Html::a($model->name, ["view", "id" => $model->id, "tournament_id" => $model->tournament->id]);
            },
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => \common\models\search\TeamSearch::getSearchArray($tournament->id),
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => Yii::t("app", 'Any Team ...')],
            'width' => "25%",
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'societyName',
            'vAlign' => GridView::ALIGN_MIDDLE,
            'filter' => \common\models\search\SocietySearch::getTournamentSearchArray($tournament->id),
            'filterType' => GridView::FILTER_SELECT2,
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => Yii::t("app", 'Any Society ...')],
            'width' => "25%",
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'speakerName',
            'format' => 'raw',
            'value' => function ($model, $key, $index, $widget) {
                return Html::ul([
                    ($model->speakerA) ? Html::a($model->speakerA->name, ["user/view", "id" => $model->speakerA->id]) : \common\models\User::NONE,
                    ($model->speakerB) ? Html::a($model->speakerB->name, ["user/view", "id" => $model->speakerB->id]) : \common\models\User::NONE
                ],
                    ["encode" => false]);
            },
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => \common\models\search\TeamSearch::getSpeakerSearchArray($tournament->id),
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => Yii::t("app", 'Any Speaker ...')],
            'width' => "25%",
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'language_status',
            'label' => Yii::t("app", 'Language'),
            'value' => function ($model, $key, $index, $widget) {
                return \common\models\User::getLanguageStatusLabel($model->language_status, true);
            },
            'visible' => $tournament->has_esl,
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => \common\models\User::getLanguageStatusLabel(null, true),
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => Yii::t("app", 'Any Language ...')],
        ],
        [
            'class' => 'kartik\grid\ActionColumn',
            'template' => '{active}&nbsp;&nbsp;{view}&nbsp;&nbsp;{update}&nbsp;&nbsp;{delete}',
            'dropdown' => false,
            'vAlign' => 'middle',
            'buttons' => [
                "active" => function ($url, $model) {
                    return Html::a(\kartik\helpers\Html::icon("pause"), $url, [
                        'title' => Yii::t('app', 'Toogle Active'),
                        'data-pjax' => '1',
                        'data-toggle-active' => $model->id
                    ]);
                }
            ],
            'urlCreator' => function ($action, $model, $key, $index) {
                return \yii\helpers\Url::to(["team/" . $action, "id" => $model->id, "tournament_id" => $model->tournament->id]);
            },
            'viewOptions' => ['label' => \kartik\helpers\Html::icon("folder-open"), 'title' => Yii::t("app", 'View {modelClass}', ['modelClass' => 'Team']), 'data-toggle' => 'tooltip'],
            'updateOptions' => ['title' => Yii::t("app", 'Update {modelClass}', ['modelClass' => 'Team']), 'data-toggle' => 'tooltip'],
            'deleteOptions' => ['title' => Yii::t("app", 'Delete {modelClass}', ['modelClass' => 'Team']), 'data-toggle' => 'tooltip'],
            'width' => '100px',
        ],
    ];

    $modolo = $stat["active"] % 4;
    $missing = ($modolo == 0) ? 0 : 4 - $modolo;

    $stats = '<table class="col-xs-12 col-md-4">
                <tr>
                    <th>Missing Teams</th>
                    <th>Inactive Teams</th>
                    <th>Swing Teams</th>
                </tr>
                <tr>
                    <td><b class="' . (($modolo == 0) ? "text-success" : "text-danger") . '">' . Yii::$app->formatter->asDecimal($missing, 0) . '</b></td>
                    <td>' . $stat["inactive"] . '</td>
                    <td>' . $stat["swing"] . '</td>
                </tr>
            </table>';

    if ($tournament->status < Tournament::STATUS_CLOSED) {
        $toolbar = [
            ['content' =>
                Html::a(\kartik\helpers\Html::icon("plus") . "&nbsp" . Yii::t('app', 'Add'), ['create', "tournament_id" => $tournament->id], ['data-pjax' => 0, 'class' => 'btn btn-success'])
                . ' ' .
                Html::a(\kartik\helpers\Html::icon("repeat") . "&nbsp" . Yii::t('app', 'Reload'), ['index', "tournament_id" => $tournament->id], ['data-pjax' => 1, 'class' => 'btn btn-default', 'title' => Yii::t('kvgrid', 'Reload')])
                . ' ' .
                Html::a(\kartik\helpers\Html::icon("import") . "&nbsp" . Yii::t('app', 'Import'), ['import', "tournament_id" => $tournament->id], ['data-pjax' => 0, 'class' => 'btn btn-default'])
            ],
            //'{export}',
            '{toggleData}',
        ];
    } else {
        $toolbar = [
            //'{export}',
            '{toggleData}',
        ];
    }

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $gridColumns,
        'id' => 'teams',
        'pjax' => true,
        'pjaxSettings' => [
            'options' => [
                'enablePushState' => false,
            ]
        ],
        'showPageSummary' => false,
        'responsive' => true,
        'hover' => true,
        'floatHeader' => true,
        'floatHeaderOptions' => ['scrollingTop' => 100],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => Html::encode($this->title),
            'footer' => false,
            'before' => $stats,
        ],
        'toolbar' => $toolbar,
    ])
    ?>
</div>
