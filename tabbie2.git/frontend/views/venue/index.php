<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\models\Venue;
use common\models\Tournament;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Venues');
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="venue-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<? if ($tournament->status < Tournament::STATUS_CLOSED): ?>
		<?=
		Html::a(Yii::t('app', 'Create {modelClass}', [
			'modelClass' => 'Venue',
		]), ['create', "tournament_id" => $tournament->id], ['class' => 'btn btn-success'])
		?>
		<?=
		Html::a(Yii::t('app', 'Import {modelClass}', [
			'modelClass' => 'Venues',
		]), ['import', "tournament_id" => $tournament->id], ['class' => 'btn btn-default'])
		?>
		<? endif; ?>
	</p>

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
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'name',
			'pageSummary' => 'Page Total',
		],
		[
			'class' => 'kartik\grid\ActionColumn',
			'width' => "100px",
			'template' => '{active}&nbsp;&nbsp;{view}&nbsp;&nbsp;{update}&nbsp;&nbsp;{delete}',
			'dropdown' => false,
			'vAlign' => 'middle',
			'buttons' => [
				"active" => function ($url, $model) {
					return Html::a("<span class='fa fa-user-times'></span>", $url, [
						'title' => Yii::t('app', 'Toogle Active'),
						'data-pjax' => '0',
						'data-toggle-active' => $model->id
					]);
				}
			],
			'urlCreator' => function ($action, $model, $key, $index) {
				return \yii\helpers\Url::to(["venue/" . $action, "id" => $model->id, "tournament_id" => $model->tournament->id]);
			},
			'viewOptions' => ['label' => '<i class="glyphicon glyphicon-folder-open"></i>', 'title' => Yii::t("app", 'View {modelClass}', ['modelClass' => 'Venue']), 'data-toggle' => 'tooltip'],
			'updateOptions' => ['title' => Yii::t("app", 'Update {modelClass}', ['modelClass' => 'Venue']), 'data-toggle' => 'tooltip'],
			'deleteOptions' => ['title' => Yii::t("app", 'Delete {modelClass}', ['modelClass' => 'Venue']), 'data-toggle' => 'tooltip'],
			'width' => '100px'
		],
	];

	echo GridView::widget([
		'dataProvider' => $dataProvider,
		'columns' => $gridColumns,
		'id' => 'venues',
		'pjax' => true,
		'showPageSummary' => false,
		'responsive' => true,
		'hover' => true,
		'floatHeader' => false,
		'floatHeaderOptions' => ['scrollingTop' => '150'],
	])
	?>
</div>
