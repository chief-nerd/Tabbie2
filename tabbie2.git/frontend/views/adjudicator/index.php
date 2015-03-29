<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use common\models\Adjudicator;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\AdjudicatorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Adjudicators');
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="adjudicator-index">

	<?php \yii\widgets\Pjax::begin(); ?>
	<div class="row">
		<div class="col-md-8">
			<h1><?= Html::encode($this->title) ?></h1>

			<p>
				<?=
				Html::a(Yii::t('app', 'Create {modelClass}', [
					'modelClass' => 'Adjudicator',
				]), ['create', "tournament_id" => $tournament->id], ['class' => 'btn btn-success'])
				?>
				<?=
				Html::a(Yii::t('app', 'Import {modelClass}', [
					'modelClass' => 'Adjudicator',
				]), ['import', "tournament_id" => $tournament->id], ['class' => 'btn btn-default'])
				?>
				<?=
				Html::a(Yii::t('app', 'Reset watched', [
					'modelClass' => 'Adjudicator',
				]), ['resetwatched', "tournament_id" => $tournament->id], ['class' => 'btn btn-default'])
				?>
			</p>
		</div>

		<div class="col-md-4">
			<table class="table">
				<tr>
					<th>Average Adjudicator per Room</th>
					<th>Active</th>
					<th>Inactive</th>
				</tr>
				<tr>
					<td><?
						$average = ($stat["venues"] != 0) ? $stat["amount"] / $stat["venues"] : 0;

						echo "<b class='" . (($average >= 2) ? "text-success" : "text-danger") . "'>" . $average . "</b>";
						?></td>
					<td><?= $stat["amount"] ?></td>
					<td><?= $stat["inactive"] ?></td>
				</tr>
			</table>
		</div>
	</div>
	<?
	$gridColumns = [
		[
			'class' => '\kartik\grid\SerialColumn',
		],
		[
			'class' => 'kartik\grid\BooleanColumn',
			'attribute' => 'active',
			'vAlign' => 'middle',
		],
		[
			'class' => '\kartik\grid\BooleanColumn',
			'attribute' => 'can_chair',
			'width' => '5%',
		],
		[
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'name',
			'filterType' => GridView::FILTER_SELECT2,
			'filter' => \common\models\search\AdjudicatorSearch::getSearchArray($tournament->id),
			'filterWidgetOptions' => [
				'pluginOptions' => ['allowClear' => true],
			],
			'filterInputOptions' => ['placeholder' => 'Any Adjudicator'],
		],
		[
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'societyName',
			'filterType' => GridView::FILTER_SELECT2,
			'filter' => \common\models\search\SocietySearch::getSearchArray($tournament->id),
			'filterWidgetOptions' => [
				'pluginOptions' => ['allowClear' => true],
			],
			'filterInputOptions' => ['placeholder' => 'Any Society'],
		],
		[
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'strength',
			'format' => "raw",
			'value' => function ($model, $key, $index, $widget) {
				return "( $model->strength )&nbsp;&nbsp;" . Adjudicator::translateStrength($model->strength);
			},
			'width' => '15%',
		],
		[
			'class' => '\kartik\grid\BooleanColumn',
			'attribute' => 'are_watched',
			'trueIcon' => '<span class="glyphicon glyphicon-eye-open text-success"></span>',
			'falseIcon' => '<span class="glyphicon glyphicon-eye-close text-muted"></span>',
			'vAlign' => 'middle',
			'width' => '5%',
		],
		[
			'class' => 'kartik\grid\ActionColumn',
			'template' => '{active}&nbsp;&nbsp;{watch}&nbsp;&nbsp;{view}&nbsp;&nbsp;{update}&nbsp;&nbsp;{delete}',
			'dropdown' => false,
			'vAlign' => 'middle',
			'buttons' => [
				"watch" => function ($url, $model) {
					return Html::a("<span class='glyphicon glyphicon-eye-open'></span>", $url, [
						'title' => Yii::t('app', 'Toogle Watch'),
						'data-pjax' => '0',
						'data-toggle-active' => $model->id
					]);
				},
				"active" => function ($url, $model) {
					return Html::a("<span class='fa fa-user-times'></span>", $url, [
						'title' => Yii::t('app', 'Toogle Active'),
						'data-pjax' => '0',
						'data-toggle-active' => $model->id
					]);
				}
			],
			'urlCreator' => function ($action, $model, $key, $index) {
				return \yii\helpers\Url::to(["adjudicator/" . $action, "id" => $model->id, "tournament_id" => $model->tournament->id]);
			},
			'viewOptions' => ['label' => '<i class="glyphicon glyphicon-folder-open"></i>', 'title' => Yii::t("app", "View Adjudicator"), 'data-toggle' => 'tooltip'],
			'updateOptions' => ['title' => Yii::t("app", "Update Adjudicator"), 'data-toggle' => 'tooltip'],
			'deleteOptions' => ['title' => Yii::t("app", "Delete Adjudicator"), 'data-toggle' => 'tooltip'],
			'width' => '122px',
		],
	];

	echo GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => $gridColumns,
		'id' => 'adjudicators',
		'pjax' => true,
		'showPageSummary' => false,
		'responsive' => true,
		'hover' => true,
		'floatHeader' => true,
		'floatHeaderOptions' => ['scrollingTop' => 100],
		'toolbar' => [
			['content' =>
				Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'Add Adjudicator'), ["adjudicator/create", "tournament_id" => $tournament->id], ['class' => 'btn btn-success'])
			]
		]
	])
	?>
	<?php \yii\widgets\Pjax::end(); ?>
</div>
