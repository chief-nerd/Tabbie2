<?php

use common\models\Adjudicator;
use common\models\Panel;
use common\models\Country;

/* @var $model Adjudicator */
/* @var $round_id Integer */

$this->context->layout = null;
?>
<div style="float:left; height:80px; margin-right: 10px;">
	<?= $model->user->getPictureImage(80, 80) ?>
</div>
<table>
	<colgroup>
		<col width="80"/>
	</colgroup>
	<tr>
		<th><?= Yii::t("app", "Strength") ?></th>
		<td><?= $model->getStrengthOutput() ?></td>
	</tr>
	<tr>
		<th><?= Yii::t("app", "Region") ?></th>
		<td><?= Country::getRegionLabel($model->society->country->region_id) ?></td>
	</tr>
	<tr>
		<th><?= Yii::t("app", "Chaired") ?></th>
		<?
		$chaired = Panel::find()->joinWith("adjudicatorInPanels")->joinWith("debate")->where([
			"panel.tournament_id" => $model->tournament_id,
			"adjudicator_id" => $model->id,
			"function"       => Panel::FUNCTION_CHAIR,
		])->andWhere("round_id != " . $round_id)->count();
		?>
		<td><?= $chaired ?></td>
	</tr>
	<tr>
		<th><?= Yii::t("app", "Feedback") ?></th>
		<td>@todo</td>
	</tr>
	<tr>
		<th><?= Yii::t("app", "Pointer") ?></th>
		<td>@todo</td>
	</tr>
</table>
<div class="clear"></div>