<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Panel */

$this->title = Yii::t("app", 'Preset Panel #') . $model->id;
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Panels'), 'url' => ['index', "tournament_id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="panel-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'used',
            [
                'label' => Yii::t("app", 'Average Panel Strength'),
                'attribute' => 'strength',
            ],
        ],
    ]) ?>

    <h2><?= Yii::t("app", "Adjudicators") ?></h2>

    <div>
        <?
        foreach ($model->getAdjudicatorsObjects() as $adj)
            $list[] = Html::a($adj->name, ["adjudicator/view", "id" => $adj->id, "tournament_id" => $tournament->id]);

        echo Html::ul($list,
            ["encode" => false]);

        ?>
    </div>
</div>