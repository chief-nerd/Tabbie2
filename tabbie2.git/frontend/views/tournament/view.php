<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Tournament */

$this->title = $model->fullname;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tournaments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tournament-view">

    <div class="row">
        <div class="col-sm-8">
            <div class="col-sm-2"><img class="img-rounded img-responsive" src="<?= $model["logo"] ?>" alt="<?= $model["fullname"] ?> Logo"></div>
            <div class="col-sm-10"><h1><?= Html::encode($this->title) ?></h1></div>
        </div>
        <div class="col-sm-4 text-right">
            <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('app', 'Import from Reg'), ['import', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
            <?=
            Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ])
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <? foreach ($model->getRounds()->where(["published" => 1])->all() as $round): ?>
                <div class="col-md-3">
                    Motion Round <?= $round->id ?>:
                </div>
                <div class="col-md-9">
                    <?= $round->motion ?>
                </div>
                <div class="hidden">
                    <?= $round->infoslide ?>
                </div>
            <? endforeach; ?>
        </div>
        <div class="col-md-4">
            <?=
            DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'convenorUser.name:text:Convenor',
                    'tabmasterUser.name:text:Tabmaster',
                    'start_date',
                    'end_date',
                ],
            ])
            ?>
        </div>
    </div>

</div>
