<?php

use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="row">
    <a href="<?= Url::to(['view', 'id' => $tournament->id]) ?>">
        <div class="col-xs-3 col-sm-2">
            <?= Html::img($tournament->logo, ["class" => "img-responsive"]) ?>
        </div>
        <div class="col-xs-9 col-sm-10">
            <h2><?= Html::encode($tournament->name) ?></h2>
            <h4><?= Html::encode($tournament->start_date) ?> - <?= Html::encode($tournament->end_date) ?></h4>
        </div>
    </a>
</div>
