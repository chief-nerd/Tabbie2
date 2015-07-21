<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use common\models\Adjudicator;
use common\models\Tournament;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\AdjudicatorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Adjudicators');
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="adjudicator-index">
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
            'class' => '\kartik\grid\BooleanColumn',
            'attribute' => 'can_chair',
            'width' => '5%',
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'name',
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => \common\models\search\AdjudicatorSearch::getSearchArray($tournament->id),
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => Yii::t("app", 'Any Adjudicator ...')],
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'societyName',
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => \common\models\search\SocietySearch::getTournamentSearchArray($tournament->id),
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => Yii::t("app", 'Any Society ...')],
            'width' => '20%',
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'strength',
            'format' => "raw",
            'value' => function ($model, $key, $index, $widget) {
                return $model->getStrengthOutput();
            },
            'width' => '15%',
        ],
        [
            'class' => '\kartik\grid\BooleanColumn',
            'attribute' => 'are_watched',
            'trueIcon' => \kartik\helpers\Html::icon("eye-open", ['class' => 'text-success']),
            'falseIcon' => \kartik\helpers\Html::icon("eye-close", ['class' => 'text-muted']),
            'vAlign' => 'middle',
            'width' => '5%',
        ],
        [
            'class' => 'kartik\grid\ActionColumn',
            'template' => '{active}&nbsp;&nbsp;{watch}&nbsp;&nbsp;{view}&nbsp;&nbsp;{update}&nbsp;&nbsp;{delete}',
            'dropdown' => false,
            'vAlign' => 'middle',
            'buttons' => [
                "watch" => function ($url, $model) {
                    return Html::a(\kartik\helpers\Html::icon("eye-open"), $url, [
                        'title' => Yii::t('app', 'Toogle Watch'),
                        'data-pjax' => '1',
                        'data-toggle-active' => $model->id
                    ]);
                },
                "active" => function ($url, $model) {
                    return Html::a(\kartik\helpers\Html::icon("pause"), $url, [
                        'title' => Yii::t('app', 'Toogle Active'),
                        'data-pjax' => '1',
                        'data-toggle-active' => $model->id
                    ]);
                }
            ],
            'urlCreator' => function ($action, $model, $key, $index) {
                return \yii\helpers\Url::to(["adjudicator/" . $action, "id" => $model->id, "tournament_id" => $model->tournament->id]);
            },
            'viewOptions' => ['label' => \kartik\helpers\Html::icon("folder-open"), 'title' => Yii::t("app", 'View {modelClass}', ['modelClass' => 'Adjudicator']), 'data-toggle' => 'tooltip'],
            'updateOptions' => ['title' => Yii::t("app", 'Update {modelClass}', ['modelClass' => 'Adjudicator']), 'data-toggle' => 'tooltip'],
            'deleteOptions' => ['title' => Yii::t("app", 'Delete {modelClass}', ['modelClass' => 'Adjudicator']), 'data-toggle' => 'tooltip'],
            'width' => '122px',
        ],
    ];

    $average = ($stat["venues"] != 0) ? $stat["amount"] / $stat["venues"] : 0;
    $stats = '<table class="col-xs-12 col-md-4">
                <tr>
                    <th>Average Adjudicator per Room</th>
                    <th>Active</th>
                    <th>Inactive</th>
                </tr>
                <tr>
                    <td><b class="' . (($average >= 2) ? "text-success" : "text-danger") . '">' . Yii::$app->formatter->asDecimal($average, 1) . '</b></td>
                    <td>' . $stat["amount"] . '</td>
                    <td>' . $stat["inactive"] . '</td>
                </tr>
            </table>';

    if ($tournament->status < Tournament::STATUS_CLOSED) {
        $toolbar = [
            ['content' =>
				 Html::a(\kartik\helpers\Html::icon("plus"), ['create', "tournament_id" => $tournament->id], [
					 'title'     => Yii::t('app', 'Add new element'),
					 'data-pjax' => 0,
					 'class'     => 'btn btn-default'
				 ]) .
				 Html::a(\kartik\helpers\Html::icon("repeat"), ['index', "tournament_id" => $tournament->id], [
					 'data-pjax' => 1,
					 'class'     => 'btn btn-default',
					 'title'     => Yii::t('app', 'Reload content'),
				 ]) .
				 Html::a(\kartik\helpers\Html::icon("import"), ['import', "tournament_id" => $tournament->id], [
					 'data-pjax' => 0,
					 'class'     => 'btn btn-default',
					 'title'     => Yii::t('app', 'Import via CSV File'),
				 ])
            ],
            [
				'content' => Html::a(\kartik\helpers\Html::icon("eye-close") . "&nbsp;" . Yii::t('app', 'Reset'), [
					'resetwatched', "tournament_id" => $tournament->id
				], [
					'class' => 'btn btn-default',
					'title' => Yii::t('app', 'Reset watcher flag'),
				]),
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
		'emptyText' => Yii::t("app", "<b>This tournament has no {object}s yet.</b><br>{add} a {object} or {import} them via csv File.", [
			"object" => "adjudicator",
			"add"    => Html::a(\kartik\helpers\Html::icon("plus") . "&nbsp" . Yii::t('app', 'Add'), ['create', "tournament_id" => $tournament->id], ['data-pjax' => 0, 'class' => 'btn btn-success']),
			"import" => Html::a(\kartik\helpers\Html::icon("import") . "&nbsp" . Yii::t('app', 'Import'), ['import', "tournament_id" => $tournament->id], ['data-pjax' => 0, 'class' => 'btn btn-primary'])
		]),
        'id' => 'adjudicators',
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
            'before' => $stats,
        ],
        'toolbar' => $toolbar,
    ])
    ?>
</div>
