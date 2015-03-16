<?php

use kartik\grid\GridView;

$this->context->menuItems = [
	['label' => 'Run', 'url' => "#run", "linkOptions" => ["class" => "run"]],
];

$this->title = "Round " . $round->number . " Draw";
?>
<div class="row">
	<div class="col-sm-12">
		<?
		$gridColumns = [
			[
				'class' => '\kartik\grid\DataColumn',
				'attribute' => 'venue.name',
				'label' => 'Venue',
			],
			[
				'class' => '\kartik\grid\DataColumn',
				'attribute' => 'og_team.name',
				'label' => "OG Team",
			],
			[
				'class' => '\kartik\grid\DataColumn',
				'attribute' => 'oo_team.name',
				'label' => "OO Team",
			],
			[
				'class' => '\kartik\grid\DataColumn',
				'attribute' => 'cg_team.name',
				'label' => 'CG Team',
			],
			[
				'class' => '\kartik\grid\DataColumn',
				'attribute' => 'co_team.name',
				'label' => 'CO Team',
			],
			[
				'class' => '\kartik\grid\DataColumn',
				'attribute' => 'panel',
				'label' => 'Adjudicator',
				'format' => 'raw',
				'width' => '40%',
				'value' => function ($model, $key, $index, $widget) {
					$list = array();
					$panel = common\models\Panel::findOne($model->panel_id);
					if ($panel) {
						$chair = common\models\AdjudicatorInPanel::findOne([
							"panel_id" => $panel->id,
							"function" => "1",
						]);

						foreach ($panel->adjudicators as $adj) {
							if ($adj->id == $chair->adjudicator_id) {
								array_unshift($list, "<b>" . $adj->user->name . "</b>");
							}
							else
								$list[] = $adj->user->name;
						}

						return implode(", ", $list);
					}
					return "";
				}
			],
		];

		echo GridView::widget([
			'dataProvider' => $dataProvider,
			'columns' => $gridColumns,
			'showPageSummary' => false,
			'bootstrap' => true,
			'hover' => true,
			'responsive' => false,
			'floatHeader' => true,
			'layout' => "{items}\n{pager}",
			'floatHeaderOptions' => ['scrollingTop' => 50],
		])
		?>
	</div>
</div>
<? if ($round->infoslide): ?>
	<? $motionStyle = "display:none;"; ?>
	<div class="row" id="drawdisplay" style="width: 90%; margin: 0 auto;">
		<center>
			<?= yii\helpers\Html::button("Show Info Slide", ["disabled" => "disabled", "id" => 'infoslide', "class" => "btn btn-success"]) ?>
			<?= yii\helpers\Html::button("Show Motion", ["disabled" => "disabled", "class" => "btn btn-success", "id" => 'motion']) ?>
		</center>
		<div class="col-sm-12" id="infoslideContent" style="display:none; margin-bottom: 100%">
			<h2><?= $round->infoslide ?></h2>
		</div>
		<div class="col-sm-12 text-center" id="motionContent"
		     data-href="<?= yii\helpers\Url::to(["display/start", "id" => $round->id, "tournament_id" => $round->tournament_id]) ?>"
		     style="display:none; margin-bottom: 100%">
			<h1><?= $round->motion ?></h1>
		</div>
	</div>
<? else: ?>
	<div class="row" id="drawdisplay" style="width: 90%; margin: 0 auto;">
		<center>
			<?= yii\helpers\Html::button("Show Motion", ["disabled" => "disabled", "class" => "btn btn-success", "id" => 'motion']) ?>
		</center>
		<div class="col-sm-12 text-center" id="motionContent"
		     data-href="<?= yii\helpers\Url::to(["display/start", "id" => $round->id, "tournament_id" => $round->tournament_id]) ?>"
		     style="display:none; margin-bottom: 100%">
			<h1><?= $round->motion ?></h1>
		</div>
	</div>
<? endif; ?>
</div>
</div>
</div>


</div>