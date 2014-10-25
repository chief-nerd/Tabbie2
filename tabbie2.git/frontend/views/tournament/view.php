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
        <div class="col-sm-9">
            <div class="col-sm-2"><img class="img-rounded img-responsive" src="<?= $model["logo"] ?>" alt="<?= $model["fullname"] ?> Logo" height="150"></div>
            <div class="col-sm-10"><h1><?= Html::encode($this->title) ?></h1></div>
        </div>
        <div class="col-sm-3 text-right">
            <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
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
        <?=
        DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'convenorUser.name:text:Convenor',
                'tabmasterUser.name:text:Tabmaster',
                'fullname',
                'start_date',
                'end_date',
                'time',
            ],
        ])
        ?>
    </div>

</div>
