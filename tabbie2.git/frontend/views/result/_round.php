<?php
use common\models\Round;

/** @var Round $model */

$posStat = $model->getAmountOfResults();
?>
<div class="motion-balance">
    <div class="row">
        <div class="col-xs-12">
            <h2><?= $model->getName() ?></h2>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-4 text-center">

            <?= $model->generateBalanceSVG(180) ?>

        </div>
        <div class="col-xs-12 col-sm-8">

            <table class="table">
                <tr>
                    <th></th>
                    <th><?= Yii::t("app", "1st") ?></th>
                    <th><?= Yii::t("app", "2nd") ?></th>
                    <th><?= Yii::t("app", "3rd") ?></th>
                    <th><?= Yii::t("app", "4th") ?></th>
                </tr>
                <? for ($i = 0; $i < 4; $i++): ?>
                    <tr>
                        <th><?= \common\models\Team::getPosLabel($i) ?></th>
                        <? for ($a = 0; $a < 4; $a++): ?>
                            <td><?= Yii::$app->formatter->asPercent(($posStat[$i][4] > 0) ? $posStat[$i][$a] / $posStat[$i][4] : 0) ?></td>
                        <? endfor; ?>
                    </tr>
                <? endfor; ?>
            </table>
        </div>
    </div>
</div>
