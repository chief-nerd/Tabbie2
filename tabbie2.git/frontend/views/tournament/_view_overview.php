<?
use kartik\helpers\Html;
use common\models\Tournament;
use common\models\Panel;
use common\models\Debate;
use common\models\Round;

/** @var Tournament $model */
?>
<div class="row" id="tournament_title">
    <div class="col-xs-12 col-md-12 col-lg-8" style="margin-bottom: 20px">
        <div class="col-xs-6 col-sm-2 block-center">
            <?= $model->getLogoImage("auto", "100") ?>
        </div>
        <div class="col-xs-6 col-sm-10 tournament_title">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
    </div>
    <?php
    if ($model->status === Tournament::STATUS_RUNNING): ?>
        <div class="col-xs-12 col-md-8 col-lg-8">
            <?
            if (is_int(Yii::$app->user->id)) {
                /** @var Round $lastRound */
                $lastRound = $model->getLastRound();
                if ($lastRound instanceof Round) {
                    $info = $lastRound->getLastDebateInfo();
                    if ($info) {
                        echo $this->render('_view_buttons', [
                            'info' => $info,
                            'model' => $model
                        ]);

                        echo $this->render('_view_roundinfo', [
                            'info' => $info,
                        ]);

                        if ($info["pos"] == Panel::FUNCTION_CHAIR && $info["debate"] instanceof Debate) {
                            echo $this->render("_view_debateinfo", [
                                "model" => $info["debate"]
                            ]);
                        }
                    } else {
                        echo $this->render('_view_noopenround', [
                            'model' => $model
                        ]);
                    }
                } else {
                    echo $this->render('_view_reginfo', [
                        'model' => $model,
                    ]);
                }
            }
            ?>
        </div>
    <? endif; ?>
    <div class="col-xs-12 col-md-4 col-lg-4">
        <?
        echo $this->render('_view_tournamentinfo', [
            'model' => $model,
        ]);
        ?>
    </div>
</div>