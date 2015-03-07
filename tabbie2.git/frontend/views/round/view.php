<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\DetailView;
use common\models\search\DebateSearch;
use kartik\sortable\Sortable;
use kartik\popover\PopoverX;

/* @var $this yii\web\View */
/* @var $model common\models\Round */

$this->title = "Round #" . $model->number;
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rounds'), 'url' => ['index', "tournament_id" => $tournament->id]];
$this->params['breadcrumbs'][] = "#" . $model->number;
?>
<div class="round-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <? if (!$model->published): ?>
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
        <? endif; ?>
        <?= Html::a(Yii::t('app', 'Print Ballots'), ['printballots', 'id' => $model->id, "tournament_id" => $tournament->id], ['class' => 'btn btn-default']) ?>
    </p>
    <?
    $attributes = [];
    $attributes[] = [
        "label" => 'Round Status',
        'value' => common\models\Round::statusLabel($model->status),
    ];
    $attributes[] = 'motion:ntext';
    if ($model->infoslide)
        $attributes[] = 'infoslide:ntext';
    if ($model->displayed)
        $attributes[] = 'prep_started';
    $attributes[] = 'time:text:Creation Time';

    echo DetailView::widget([
        'model' => $model,
        'attributes' => $attributes,
    ])
    ?>

    <?= $this->render("_filter", ["model" => $model, "debateSearchModel" => $debateSearchModel]) ?>
    <hr>

    <?
    $gridColumns = [
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'venue',
            'label' => 'Venue',
            'width' => '8%',
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
                        $popup_obj = PopoverX::widget([
                                    'header' => $adj->name,
                                    'size' => 'md',
                                    'placement' => PopoverX::ALIGN_TOP,
                                    'content' => $popcontent,
                                    'footer' =>
                                    Html::a('Move', ["adjudicator/move", "id" => $adj->id, "debate" => $model->id, "tournament_id" => $model->tournament_id], ['class' => 'moveAdj btn btn-sm btn-primary']) .
                                    Html::a('View more', ["adjudicator/view", "id" => $adj->id, "tournament_id" => $model->tournament_id], ['class' => 'btn btn-sm btn-default']),
                                    'toggleButton' => [
                                        'label' => $adj->user->name,
                                        'class' => 'btn btn-sm adj ' . common\models\Adjudicator::starLabels($adj->strength),
                                        "data-id" => $adj->id,
                                        "data-strength" => $adj->strength,
                                        "data-href" => yii\helpers\Url::to(["adjudicator/popup", "id" => $adj->id, "round_id" => $model->round_id, "tournament_id" => $model->tournament_id]),
                                    ],
                        ]);

                        if ($adj->id == $model->getChair()->id) {
                            array_unshift($list, array('content' => $popup_obj));
                        } else
                            $list[]['content'] = $popup_obj;
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
                'id' => 'debateDraw',
            ])
            ?>

</div>
