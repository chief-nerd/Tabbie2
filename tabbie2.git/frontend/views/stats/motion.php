<?php
use kartik\helpers\Html;
use common\models\Round;

?>

<h3><?php echo Yii::t("app", "Motions") ?></h3>
<div class="row">
    <div class="col-xs-12">
        <ul class="list-group">
            <? foreach ($model->getRounds()->where(["displayed" => 1])->all() as $round):
                /** @var Round $round */
                ?>
                <li class="list-group-item">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-2">
                            <?
                            if ($model->isTabMaster(Yii::$app->user->id) || $model->isConvenor(Yii::$app->user->id)):
                                ?>
                                <?= Html::a($round->name, ["round/view", "id" => $round->id, "tournament_id" => $model->id]); ?>
                            <? else: ?>
                                <?= $round->name ?>
                            <? endif; ?>
                        </div>
                        <div class="col-xs-12 col-sm-10 col-md-8">
                            <?= Html::encode($round->motion) ?>
                        </div>
                        <?
                        if ($model->status === \common\models\Tournament::STATUS_CLOSED): ?>
                            <div class="col-xs-12 col-sm-2 col-md-2">
                                <div class="balance-frame center-block">
                                    <?= $round->generateBalanceSVG() ?>
                                </div>
                            </div>
                        <? endif; ?>
                    </div>
                </li>
            <? endforeach; ?>
        </ul>
    </div>
</div>
<div class="clear"></div>