<?php
use kartik\helpers\Html;
use common\models\Round;

?>

<h3><?php echo Yii::t("app", "Motions") ?></h3>
<div class="row">
    <div class="col-xs-12">
        <ul class="list-group">
            <? foreach ($model->getRounds()->where([
                "displayed" => 1
            ])->all() as $round):

                /** @var Round $round */
                $posStat = $round->getAmountOfResults();
                ?>
                <li class="list-group-item">

                    <div class="row">
                        <div class="col-xs-12 col-sm-4 text-center">

                            <?= $round->generateBalanceSVG(200) ?>

                        </div>
                        <div class="col-xs-12 col-sm-8">

                            <?= Html::a("<h2 style='margin-top:0'>" . $round->name . "</h2>", ["round/view", "id" => $round->id, "tournament_id" => $model->id]); ?>

                            <p><?= Html::encode($round->motion) ?></p>

                            <? if ($model->status === \common\models\Tournament::STATUS_CLOSED): ?>
                                <table class="table slim">
                                    <tr>
                                        <th></th>
                                        <? for ($i = 1; $i <= 4; $i++): ?>
                                            <th><?= Yii::$app->formatter->asOrdinal($i) ?></th>
                                        <? endfor; ?>
                                    </tr>
                                    <? for ($i = 0; $i < 4; $i++): ?>
                                        <tr>
                                            <th>
                                                <div class="hidden-xs"><?= \common\models\Team::getPosLabel($i) ?></div>
                                                <div
                                                    class="visible-xs-inline-block"><?= strtoupper(\common\models\Team::getPos($i)) ?></div>
                                            </th>
                                            <? for ($a = 0; $a < 4; $a++): ?>
                                                <td>
                                                    <?= Yii::$app->formatter->asPercent(($posStat[$i][4] > 0) ? $posStat[$i][$a] / $posStat[$i][4] : 0) ?></td>
                                            <? endfor; ?>
                                        </tr>
                                    <? endfor; ?>
                                </table>
                            <? endif; ?>
                        </div>
                    </div>
                </li>
            <? endforeach; ?>
        </ul>
    </div>
</div>
<div class="clear"></div>