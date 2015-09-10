<?php
/**
 * _item.php File
 * @package  Tabbie2
 * @author   jareiter
 * @version
 *
 * @var \common\models\Debate $model
 * @var \common\models\Round  $round
 */
$round = $model->round;
$th_class = "col-xs-12 col-sm-2 col-md-1 col-lg-1";
$td_class = "col-xs-12 col-sm-10 col-md-11 col-lg-11";

$teamId = (isset($teamId)) ? $teamId : 0;
?>
<div class="panel panel-default">
	<div class="panel-heading"><b><?= $round->name ?></b></div>
	<div class="panel-body">
		<div class="row">
			<div class="<?= $th_class ?>"><?= Yii::t("app", "Motion:") ?></div>
			<div class="<?= $td_class ?>"><?= $round->motion ?></div>
		</div>
		<div class="row">
			<div class="<?= $th_class ?>"><?= Yii::t("app", "Panel:") ?></div>
			<div class="<?= $td_class ?>"><?= $model->panel->getAdjudicatorsString() ?></div>
		</div>
	</div>
	<table class="table debate-history">
		<tr>
			<th><?= Yii::t("app", "Opening Government") ?></th>
			<th><?= Yii::t("app", "Opening Opposition") ?></th>
			<th><?= Yii::t("app", "Closing Government") ?></th>
			<th><?= Yii::t("app", "Closing Opposition") ?></th>
		</tr>
		<tr>
			<? foreach (\common\models\Team::getPos() as $p): ?>
				<td class="<?= ($model->{$p . "_team"}->id == $teamId) ? 'us' : "" ?>"><?= \kartik\helpers\Html::a($model->{$p . "_team"}->name, [
						"team/view",
						"id"            => $model->{$p . "_team"}->id,
						"tournament_id" => $model->tournament_id
					]) ?></td>
			<? endforeach; ?>
		</tr>
		<? if ($model->result instanceof \common\models\Result): ?>
			<tr>
				<?
				foreach (\common\models\Team::getPos() as $p): ?>
					<td width="25%" class="<?= ($model->{$p . "_team"}->id == $teamId) ? 'us' : "" ?>">
						<b><?= Yii::$app->formatter->asOrdinal($model->result->{$p . "_place"}) ?></b>
						(<?= $model->result->{$p . "_A_speaks"} ?> / <?= $model->result->{$p . "_B_speaks"} ?>)
					</td>
				<? endforeach; ?>
			</tr>
		<? endif; ?>
	</table>
</div>
