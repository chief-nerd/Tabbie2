<?php

use yii\helpers\Html;
use yii\helpers\Url;
?>
<a href="<?= Url::to(['view', 'id' => $tournament->id]) ?>">
    <div class="col-sm-2">
        <?= Html::img($tournament->logo, ["style" => "height:80px; margin: 10px;"]) ?>
    </div>
    <div class="col-sm-6">
        <h2><?= Html::encode($tournament->name) ?></h2>
        <h4><?= Html::encode($tournament->start_date) ?> - <?= Html::encode($tournament->end_date) ?></h4>
    </div>
    <div class="col-sm-4 form-group">
        <?
        $debate_id = $this->context->activeInputAvailable($tournament);
        if (isset($debate_id) && $debate_id != false) {
            echo Html::a(Yii::t('app', 'Enter Results'), [
                'result/create',
                'tournament_id' => $tournament->id,
                'debate_id' => $debate_id
                    ], ['class' => 'btn btn-lg btn-success']);
        }
        ?>
    </div>
</a>
