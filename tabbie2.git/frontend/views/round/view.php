<?php

use kartik\grid\GridView;
use kartik\popover\PopoverX;
use kartik\sortable\Sortable;
use kartik\widgets\SwitchInput;
use yii\helpers\Html;
use yii\widgets\DetailView;


/* @var $this yii\web\View */
/* @var $model common\models\Round */

\frontend\assets\RoundviewAsset::register($this);

$this->title = "Round #" . $model->number;
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rounds'), 'url' => ['index', "tournament_id" => $tournament->id]];
$this->params['breadcrumbs'][] = "#" . $model->number;
?>
<div class="round-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<div class="row">
		<div class="col-md-12">
			<? if (!$model->published): ?>
				<?= Html::a(Yii::t('app', 'Publish Tab'), ['publish', 'id' => $model->id, "tournament_id" => $tournament->id], ['class' => 'btn btn-success']) ?>
				<?= Html::a(Yii::t('app', 'Update Round Info'), ['update', 'id' => $model->id, "tournament_id" => $tournament->id], ['class' => 'btn btn-primary']) ?>
				<?=
				Html::a(Yii::t('app', 'ReDraw Round'), ['redraw', 'id' => $model->id, "tournament_id" => $tournament->id], [
					'class' => 'btn btn-default',
					'data' => [
						'confirm' => Yii::t('app', 'Are you sure you want to re-draw the round? All information will be lost!'),
						'method' => 'post',
					],
				])
				?>
			<? endif; ?>
			<?= Html::a(Yii::t('app', 'Print Ballots'), ['printballots', 'id' => $model->id, "tournament_id" => $tournament->id], ['class' => 'btn btn-default']) ?>

		</div>
	</div>
	<div class="row">
		<div class="col-md-8 text-middle">
			<?
			$attributes = [];
			$attributes[] = [
				"label" => 'Round Status',
				'value' => common\models\Round::statusLabel($model->status),
			];
			$attributes[] = 'motion:ntext';
			if ($model->infoslide)
				$attributes[] = 'infoslide:ntext';
			if ($model->displayed)
				$attributes[] = 'prep_started';
			$attributes[] = 'time:text:Creation Time';

			echo DetailView::widget([
				'model' => $model,
				'attributes' => $attributes,
			])
			?>
		</div>
		<div class="col-md-4 text-center">
			<h3 style="margin-top:0; margin-bottom:20px;">Color Palette</h3>

			<?= SwitchInput::widget([
				'name' => 'colorpattern',
				'type' => SwitchInput::RADIO,
				'value' => "strength",
				'items' => [
					['label' => Yii::t("app", "Strength"), 'value' => "strength"],
					['label' => Yii::t("app", "Gender"), 'value' => "gender"],
					['label' => Yii::t("app", "Regions"), 'value' => "region"],
				],
				'pluginOptions' => ['size' => 'medium'],
				'labelOptions' => ["style" => "width: 80px"],
				'separator' => "<br/>",
				'pluginEvents' => [
					"switchChange.bootstrapSwitch" => "function() { $('#debateDraw')[0].className = this.value; }",
				],
			]);
			/**
			 * @TODO: Save setting while filtering and reload
			 */
			?>
		</div>
	</div>

	<a name="draw"></a>
	<?= $this->render("_filter", ["model" => $model, "debateSearchModel" => $debateSearchModel]) ?>
	<?
	$gridColumns = [
		[
			'class' => 'kartik\grid\ExpandRowColumn',
			'width' => '30px',
			'value' => function ($model, $key, $index, $column) {
				return GridView::ROW_COLLAPSED;
			},
			/*'detail'=>function ($model, $key, $index, $column) {
				return Yii::$app->controller->renderPartial('_debate_details', ['model'=>$model]);
			},*/
			'headerOptions' => ['class' => 'kartik-sheet-style'],
			'detailUrl' => \yii\helpers\Url::to(['round/debatedetails', "tournament_id" => $model->tournament_id]),
		],
		[
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'venue',
			'label' => 'Venue',
			'width' => '8%',
			'format' => 'raw',
			'value' => function ($model, $key, $index, $widget) {
				if (!$model->round->published)
					return $this->render("_changeVenue", ["model" => $model]);
				else
					return $model->venue->name;
			},
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
				/** @var Debate $model */
				$list = array();
				$panel = common\models\Panel::findOne($model->panel_id);
				if ($panel) {
					$panel_chair_id = $model->getChair()->id;
					foreach ($panel->adjudicators as $adj) {

						$popcontent = "Loading...";

						$class = "label";
						$class .= " " . common\models\Adjudicator::getCSSStrength($adj->strength);
						if (isset($adj->society->country->region_id))
							$class .= " " . \common\models\Country::getCSSLabel($adj->society->country->region_id);
						if (isset($adj->user->gender))
							$class .= " " . \common\models\User::getCSSGender($adj->user->gender);

						$popup_obj = PopoverX::widget([
							'header' => $adj->name . " " . $adj->user->getGenderIcon(),
							'size' => 'md',
							'placement' => PopoverX::ALIGN_TOP,
							'content' => $popcontent,
							'footer' =>
								Html::a('Move', ["adjudicator/move", "id" => $adj->id, "debate" => $model->id, "tournament_id" => $model->tournament_id], ['class' => 'moveAdj btn btn-sm btn-primary']) .
								Html::a('View more', ["adjudicator/view", "id" => $adj->id, "tournament_id" => $model->tournament_id], ['class' => 'btn btn-sm btn-default']),
							'toggleButton' => [
								'label' => $adj->user->name,
								'class' => 'btn btn-sm adj ' . $class,
								"data-id" => $adj->id,
								"data-strength" => $adj->strength,
								"data-href" => yii\helpers\Url::to(["adjudicator/popup", "id" => $adj->id, "round_id" => $model->round_id, "tournament_id" => $model->tournament_id]),
							],
						]);

						if ($adj->id == $panel_chair_id) {
							array_unshift($list, array('content' => $popup_obj));
						}
						else
							$list[]['content'] = $popup_obj;
					}

					return Sortable::widget([
						'type' => Sortable::TYPE_GRID,
						'items' => $list,
						'disabled' => $model->round->published,
						'handleLabel' => ($model->round->published) ? '' : '<i class="glyphicon glyphicon-move"></i> ',
						'connected' => true,
						'showHandle' => true,
						'options' => [
							"data-panel" => $panel->id,
							"class" => "adj_panel",
						],
					]);
				}
				return "";
			}
		],
	];

	echo GridView::widget([
		'dataProvider' => $debateDataProvider,
		'filterModel' => $debateSearchModel,
		'columns' => $gridColumns,
		'showPageSummary' => false,
		'layout' => "{items}\n{pager}",
		'bootstrap' => true,
		'pjax' => false,
		'hover' => true,
		'responsive' => false,
		'floatHeader' => true,
		'floatHeaderOptions' => ['scrollingTop' => 100],
		'id' => 'debateDraw',
		'options' => [
			'class' => 'strength',
		]

	])
	?>

</div>
