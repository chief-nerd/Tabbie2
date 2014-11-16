<?php

use common\models\Adjudicator;
use common\models\Panel;

/* @var $model Adjudicator */
$this->context->layout = null;
?>
<div style="float:left; height:80px; margin-right: 10px;">
    <img src="<?= $model->user->getPicture() ?>" width="80" height="80">
</div>
<table>
    <colgroup>
        <col width="80"></col>
    </colgroup>
    <tr>
        <th><?= Yii::t("app", "Strength") ?></th>
        <td><?= Adjudicator::translateStrength($model->strength) ?> (<?= $model->strength ?>)</td>
    </tr>
    <tr>
        <th><?= Yii::t("app", "Chaired") ?></th>
        <?
        $chaired = Panel::find()->joinWith("adjudicatorInPanels")->joinWith("debates")->where([
                    "panel.tournament_id" => $model->tournament_id,
                    "adjudicator_id" => $model->id,
                    "function" => Panel::FUNCTION_CHAIR,
                ])->andWhere("round_id != " . $round_id)->count();
        ?>
        <td><?= $chaired ?></td>
    </tr>
    <tr>
        <th><?= Yii::t("app", "Feedback") ?></th>
        <td>@todo</td>
    </tr>
    <tr>
        <th>Pointer</th>
        <td>@todo</td>
    </tr>
</table>
<div class="clear"></div>