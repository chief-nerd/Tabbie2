<?php

use yii\helpers\Html;
use yii\helpers\Url;
?>
<a href="<?= Url::to(['view', 'id' => $tournament->id]) ?>">
    <div class="col-sm-2">
        <?= Html::img($tournament->logo, ["style" => "height:80px; margin: 10px;"]) ?>
    </div>
    <div class="col-sm-10">
        <h2><?= Html::encode($tournament->name) ?></h2>
        <h4><?= Html::encode($tournament->start_date) ?> - <?= Html::encode($tournament->end_date) ?></h4>
    </div>
</a>
