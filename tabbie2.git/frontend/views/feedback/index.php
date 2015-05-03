<?php

use common\models\search\VenueSearch;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\FeedbackSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Feedbacks');
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="feedback-index">

	<h1><?= Html::encode($this->title) ?></h1>
	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?
	$gridColumns = [
		[
			'class' => '\kartik\grid\SerialColumn'
		],
		[
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'round_number',
			'format' => 'raw',
			'value' => function ($model, $key, $index, $widget) {
				return $model->debate->round->number;
			},
			'filter' => \common\models\search\TournamentSearch::getRoundOptions($tournament->id),
			'filterType' => GridView::FILTER_SELECT2,
			'filterWidgetOptions' => [
				'pluginOptions' => ['allowClear' => true],
			],
			'filterInputOptions' => ['placeholder' => Yii::t("app", 'Any Round ...')],
		],
		[
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'venue_name',
			'format' => 'raw',
			'value' => function ($model, $key, $index, $widget) {
				return $model->debate->venue->name;
			},
			'filter' => VenueSearch::getSearchArray($tournament->id),
			'filterType' => GridView::FILTER_SELECT2,
			'filterWidgetOptions' => [
				'pluginOptions' => ['allowClear' => true],
			],
			'filterInputOptions' => ['placeholder' => Yii::t("app", 'Any Venue ...')],
		],
		'time',
		[
			'class' => 'kartik\grid\ActionColumn',
			'width' => "100px",
			'template' => '{view}&nbsp;&nbsp;{update}',
			'dropdown' => false,
			'vAlign' => 'middle',
			'urlCreator' => function ($action, $model, $key, $index) {
				return \yii\helpers\Url::to(["feedback/" . $action, "id" => $model->id, "tournament_id" => $model->debate->tournament_id]);
			},
			'viewOptions' => ['label' => '<i class="glyphicon glyphicon-folder-open"></i>', 'title' => Yii::t("app", 'View {modelClass}', ['modelClass' => 'Feedback']), 'data-toggle' => 'tooltip'],
			'updateOptions' => ['title' => Yii::t("app", 'Update {modelClass}', ['modelClass' => 'Feedback']), 'data-toggle' => 'tooltip'],
			'width' => '100px'
		]
	];

	echo GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => $gridColumns,
		'id' => 'feedback',
		'pjax' => true,
		'showPageSummary' => false,
		'responsive' => true,
		'hover' => true,
		'floatHeader' => false,
		'floatHeaderOptions' => ['scrollingTop' => '150'],
	])
	?>

</div>
