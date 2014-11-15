<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\DetailView;
use common\models\search\DebateSearch;
use kartik\sortable\Sortable;
use kartik\popover\PopoverX;

/* @var $this yii\web\View */
/* @var $model common\models\Round */

$this->title = "Round #" . $model->id;
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rounds'), 'url' => ['index', "tournament_id" => $tournament->id]];
$this->params['breadcrumbs'][] = "#" . $model->id;
?>
<div class="round-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <? if (!$model->published): ?>
        <p>
            <?= Html::a(Yii::t('app', 'Publish Tab'), ['publish', 'id' => $model->id, "tournament_id" => $tournament->id], ['class' => 'btn btn-success']) ?>
            <?= Html::a(Yii::t('app', 'Update Round Info'), ['update', 'id' => $model->id, "tournament_id" => $tournament->id], ['class' => 'btn btn-primary']) ?>
            <?=
            Html::a(Yii::t('app', 'ReDraw Round'), ['redraw', 'id' => $model->id, "tournament_id" => $tournament->id], [
                'class' => 'btn btn-default',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to re-draw the round? All information will be lost!'),
                    'method' => 'post',
                ],
            ])
            ?>
        </p>
    <? endif; ?>
    <?
    $attributes = [
        'time:text:Creation Time',
        'motion:ntext',
    ];
    $attributes[] = [
        "label" => 'Round Status',
        'value' => ($model->published) ? Yii::t("app", "Published") : Yii::t("app", "In Edit Mode"),
    ];
    if ($model->infoslide)
        $attributes[] = 'infoslide:ntext';

    echo DetailView::widget([
        'model' => $model,
        'attributes' => $attributes,
    ])
    ?>

    <?
    $gridColumns = [
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'venue.name',
            'label' => 'Venue',
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'og_team.name',
            'label' => "OG Team",
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'oo_team.name',
            'label' => "OO Team",
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'cg_team.name',
            'label' => 'CG Team',
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'co_team.name',
            'label' => 'CO Team',
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'panel',
            'label' => 'Adjudicator',
            'format' => 'raw',
            'width' => '40%',
            'value' => function ($model, $key, $index, $widget) {
                $list = array();
                $panel = common\models\Panel::findOne($model->panel_id);
                if ($panel) {
                    foreach ($panel->adjudicators as $adj) {

                        $popcontent = "Loading...";

                        $list[]['content'] = PopoverX::widget([
                                    'header' => 'Info',
                                    'size' => 'md',
                                    'placement' => PopoverX::ALIGN_TOP,
                                    'content' => $popcontent,
                                    'footer' => Html::a('View more', ["adjudicator/view", "id" => $adj->id, "tournament_id" => $model->tournament_id], ['class' => 'btn btn-sm btn-primary']),
                                    'toggleButton' => [
                                        'label' => $adj->user->name,
                                        'class' => 'btn btn-sm adj ' . common\models\Adjudicator::starLabels($adj->strength),
                                        "data-id" => $adj->user_id,
                                        "data-strength" => $adj->strength,
                                        "data-href" => yii\helpers\Url::to(["adjudicator/popup", "id" => $adj->id, "round_id" => $model->round_id, "tournament_id" => $model->tournament_id]),
                                    ],
                        ]);
                    }
                    return Sortable::widget([
                                'type' => Sortable::TYPE_GRID,
                                'items' => $list,
                                'disabled' => $model->round->published,
                                'handleLabel' => ($model->round->published) ? '' : '<i class="glyphicon glyphicon-move"></i> ',
                                'connected' => true,
                                'showHandle' => true,
                                'options' => [
                                    "data-panel" => $panel->id,
                                    "class" => "adj_panel",
                                ],
                                "pluginEvents" => [
                                    'sortupdate' => 'function(item) { console.log("sortupdate", item); }',
                                ],
                    ]);
                }
                return "";
            }
                ],
            ];

            echo GridView::widget([
                'dataProvider' => $debateDataProvider,
                'filterModel' => $debateSearchModel,
                'columns' => $gridColumns,
                'showPageSummary' => false,
                'bootstrap' => true,
                'hover' => true,
                'responsive' => false,
                'floatHeader' => true,
                'floatHeaderOptions' => ['scrollingTop' => 100],
            ])
            ?>

</div>
