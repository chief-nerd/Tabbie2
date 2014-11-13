<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\DetailView;
use common\models\search\DebateSearch;

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

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id, "tournament_id" => $tournament->id], ['class' => 'btn btn-primary']) ?>
        <?=
        Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id, "tournament_id" => $tournament->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ])
        ?>
    </p>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'motion:ntext',
            'infoslide:ntext',
            'published',
            'time',
        ],
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
            'value' => function ($model, $key, $index, $widget) {
                $list = array();
                $panel = common\models\Panel::findOne($model->panel_id);
                if ($panel) {
                    foreach ($panel->adjudicators as $adj) {
                        $chairID = common\models\AdjudicatorInPanel::findOne(["panel_id" => $model->panel_id, "function" => 1])->adjudicator_id;
                        if ($chairID == $adj->id)
                            $name = "<b>" . $adj->user->name . "</b>";
                        else
                            $name = $adj->user->name;
                        $list[] = Html::a($name, ["user/view", "id" => $adj->user->id]);
                    }
                    return Html::ul($list, ["encode" => false]);
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
                'floatHeader' => true,
                'floatHeaderOptions' => ['scrollingTop' => 100],
            ])
            ?>

</div>
