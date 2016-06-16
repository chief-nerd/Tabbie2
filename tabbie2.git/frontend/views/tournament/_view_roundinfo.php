<?php
/**
 * Created by IntelliJ IDEA.
 * User: jakob
 * Date: 01/01/16
 * Time: 23:31
 */

use kartik\helpers\Html;
use common\models\Team;
use common\models\Tournament;
use common\models\Panel;

/** @var Tournament $model */
?>
<div class="panel panel-default debate-info">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo Yii::t("app", "Round #{num} Info", ["num" => $info["debate"]->round->number]) ?></h3>
    </div>
    <div class="panel-body">
        <?php
        switch ($info["type"]) {
            case "team":
                $pos = Team::getPosLabel($info["pos"]);
                break;
            case "judge":
                $pos = Panel::getFunctionLabel($info["pos"]);
                break;
            default:
                $pos = "";
        }
        ?>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <?php echo Yii::t("app", "You are <b>{pos}</b> in room <b>{room}</b>.", [
                    "pos" => strtolower($pos),
                    "room" => $info["debate"]->venue->name,
                ]) ?></div>
            <div class="col-xs-12 col-sm-6">
                <?php echo Yii::t("app", "Round starts at: <b>{time}</b>", [
                    "time" => Yii::$app->formatter->asTime($info["debate"]->round->prep_started . "+15min", "short"),
                ]) ?>
            </div>
        </div>
        <div class="row" style="margin-top: 10px">
            <div class="col-xs-12">
                <?php echo Yii::t("app", "Motion") ?>:<br>
                <?php echo $info["debate"]->round->motion ?>
            </div>
            <? if ($info["debate"]->round->infoslide): ?>
                <div class="col-xs-12">
                    <?php echo Yii::t("app", "InfoSlide") ?>:<br>
                    <?php echo $info["debate"]->round->infoslide ?>
                </div>
            <? endif; ?>
        </div>
    </div>
</div>