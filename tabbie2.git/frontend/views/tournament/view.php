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
            <div class="col-sm-2">
                <img class="img-rounded img-responsive" src="<?= $model["logo"] ?>" alt="<?= $model["fullname"] ?> Logo"></div>
            <div class="col-sm-10">
                <h1><?= Html::encode($this->title) ?></h1>
            </div>
        </div>
        <div class="col-sm-4 text-right">

            <?
            $debate = Yii::$app->user->hasOpenFeedback($model);
            if ($debate instanceof common\models\Debate) {
                echo Html::a(Yii::t('app', 'Enter Feedback'), ['feedback/create', "id" => $debate->id, "tournament_id" => $model->id], ['class' => 'btn btn-success']);
            }
            ?>
            <?
            $debate = Yii::$app->user->hasChairedLastRound($model);
            if ($debate instanceof common\models\Debate && !$debate->result instanceof \common\models\Result) {
                echo Html::a(Yii::t('app', 'Enter Result'), ['result/create', "id" => $debate->id, "tournament_id" => $model->id], ['class' => 'btn btn-success']);
            }
            ?>

            <?= Html::a(Yii::t('app', 'Display Draw'), ['display/index', "tournament_id" => $model->id], ['class' => 'btn btn-default']) ?>
            <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8 leftcolumn">
            <? foreach ($model->getRounds()->where(["displayed" => 1])->all() as $round): ?>
                <div class="row">
                    <div class="col-md-3">
                        <?= Html::a("Motion Round #" . $round->id, ["round/view", "id" => $round->id, "tournament_id" => $model->id]); ?>
                    </div>
                    <div class="col-md-9">
                        <?= $round->motion ?>
                    </div>
                </div>
            <? endforeach; ?>
        </div>
        <div class="col-md-4 rightcolumn">
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
