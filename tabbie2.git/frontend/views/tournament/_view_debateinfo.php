<?php
/**
 * Created by IntelliJ IDEA.
 * User: jakob
 * Date: 01/01/16
 * Time: 23:36
 */

use common\models\Team;
use common\models\Debate;

/** @var Debate $model */
?>

<div class="panel panel-default debate-teams">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo Yii::t("app", "Round #{num} Teams", ["num" => $model->round->number]) ?></h3>
    </div>
    <div class="panel-body">
        <div class="row" style="margin-top: 10px">
            <? foreach (Team::getPos() as $index => $pos): ?>
                <div class="team col-xs-12 col-sm-6">
                    <?= Team::getPosLabel($index) ?>: <br>
                    <?php if ($model->{$pos . "_team"} instanceof Team): ?>
                        <?= $model->{$pos . "_team"}->name ?><br/>
                        <?= ($model->{$pos . "_team"}->speakerA instanceof \common\models\User) ?
                            $model->{$pos . "_team"}->speakerA->givenname : ""
                        ?>
                        <?= ($model->{$pos . "_team"}->speakerB instanceof \common\models\User) ?
                            " & " . $model->{$pos . "_team"}->speakerB->givenname : ""
                        ?>
                    <? endif; ?>
                </div>
            <? endforeach; ?>
        </div>
    </div>
</div>
