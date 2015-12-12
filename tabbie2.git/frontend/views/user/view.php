<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->name;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">
    <h1>User Profile</h1>

    <div class="row">
        <div class="col-sm-8">
            <p>
                <? if (Yii::$app->user->id == Yii::$app->request->get("id") || Yii::$app->user->isAdmin()): ?>
                    <?= Html::a(\kartik\helpers\Html::icon("cog") . "&nbsp" . Yii::t('app', 'Update User profile'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <? endif; ?>
            </p>
            <?=
            DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'name',
                    'email:email',
                    [
                        'label' => Yii::t("app", "Language Status"),
                        'value' => \common\models\User::getLanguageStatusLabel($model->language_status)
                    ],
                    [
                        'label' => Yii::t("app", "Last Change"),
                        'value' => Yii::$app->formatter->asDatetime($model->last_change)
                    ],
                ],
            ])
            ?>
        </div>
        <div class="col-xs-12 col-sm-4" style="padding-top: 30px;">
            <?= $model->getPictureImage(null, 180) ?>
        </div>
    </div>

    <? if (!empty($custom_attributes)): ?>
        <h2><?= Yii::t("app", "Custom Values for {tournament}", ["tournament" => $tournament->name]) ?></h2>
        <?=
    DetailView::widget([
        'model' => $custom_attributes,
    ])
        ?>
        <hr>
    <? endif; ?>

    <? if (Yii::$app->user->id == Yii::$app->request->get("id") || Yii::$app->user->isAdmin()): ?>
        <h2><?= Yii::t("app", "Individual Clashes") ?></h2>

        <p>
            <?= Html::a(\kartik\helpers\Html::icon("plus") . "&nbsp" . Yii::t('app', 'Add {object}', ['object' => Yii::t("app", 'Individual Clash')]), ['clash/create', 'user_id' => $model->id], ['class' => 'btn btn-primary']) ?>
        </p>

        <?
        $gridColumns = [
            [
                'class' => '\kartik\grid\SerialColumn',
            ],
            [
                'class' => 'kartik\grid\DataColumn',
                'attribute' => 'clashWith.name',
            ],
            /*[
                'class'     => '\kartik\grid\DataColumn',
                'attribute' => 'reason',
            ],*/
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'date',
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{update} {delete}',
                'vAlign' => 'middle',
                'urlCreator' => function ($action, $model, $key, $index) {
                    return \yii\helpers\Url::to(["clash/" . $action, "user_id" => $model->user_id, "id" => $model->id]);
                },
                'updateOptions' => ['title' => Yii::t("app", "Update Clash Info"), 'data-toggle' => 'tooltip'],
                'deleteOptions' => ['title' => Yii::t("app", "Delete Clash"), 'data-toggle' => 'tooltip'],
                'width' => '100px'
            ],
        ];

        echo GridView::widget([
            'dataProvider' => $dataClashProvider,
            'columns' => $gridColumns,
            'id' => 'clash',
            'pjax' => true,
            'showPageSummary' => false,
            'responsive' => false,
            'hover' => true,
            'floatHeader' => false,
            'emptyText' => Yii::t("app", "No clash known to the system."),
        ])
        ?>
        <hr>
    <? endif; ?>

    <h2><?= Yii::t("app", "Debate Society History") ?></h2>

    <p>
        <? if (Yii::$app->user->id == Yii::$app->request->get("id") || Yii::$app->user->isAdmin()): ?>
            <?= Html::a(\kartik\helpers\Html::icon("plus") . "&nbsp" . Yii::t('app', 'Add new society to history'), ['society/create', 'user_id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <? endif; ?>
    </p>

    <?
    $gridColumns = [
        [
            'class' => '\kartik\grid\SerialColumn',
        ],
        [
            'class' => 'kartik\grid\DataColumn',
            'attribute' => 'society.fullname',
            'vAlign' => 'middle',
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'starting',
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'ending',
            'format' => "raw",
            'value' => function ($model, $key, $index, $widget) {
                return ($model->ending) ? $model->ending : Yii::t("app", "still active");
            },
        ],
        [
            'class' => 'kartik\grid\ActionColumn',
            'template' => '{update}',
            'dropdown' => false,
            'vAlign' => 'middle',
            'urlCreator' => function ($action, $model, $key, $index) {
                return \yii\helpers\Url::to(["society/" . $action, "user_id" => $model->user_id, "id" => $model->society_id]);
            },
            'updateOptions' => ['title' => Yii::t("app", "Update Society Info"), 'data-toggle' => 'tooltip'],
            'width' => '100px'
        ],
    ];

    echo GridView::widget([
        'dataProvider' => $dataSocietyProvider,
        'columns' => $gridColumns,
        'id' => 'society',
        'pjax' => true,
        'showPageSummary' => false,
        'responsive' => true,
        'hover' => true,
        'floatHeader' => false,
    ])
    ?>
</div>
