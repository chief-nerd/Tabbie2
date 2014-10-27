<?php

use yii\helpers\Html;
use yii\helpers\Url;
?>
<a href="<?= Url::to(['view', 'id' => $model->id]) ?>">
    <div class="col-sm-2">
        <?= Html::img($model->logo, ["style" => "height:80px; margin: 10px;"]) ?>
    </div>
    <div class="col-sm-6">
        <h2><?= Html::encode($model->name) ?></h2>
        <h4><?= Html::encode($model->start_date) ?> - <?= Html::encode($model->end_date) ?></h4>
    </div>
    <div class="col-sm-4 form-group">
        <?= Html::a(Yii::t('app', 'View Team Tab'), ['draw/team', 'tournament_id' => $model->id], ['class' => 'btn btn-lg btn-default']) ?>
        <?= Html::a(Yii::t('app', 'View Speaker Tab'), ['draw/speaker', 'tournament_id' => $model->id], ['class' => 'btn btn-lg btn-default']) ?>
    </div>
</a>
