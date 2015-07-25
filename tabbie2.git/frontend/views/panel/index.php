<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\PanelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Preset Panels for next round');
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="panel-index">

	<h1><?= Html::encode($this->title) ?></h1>
	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<p>
		<?= Html::a(Yii::t('app', 'Create Panel'), ['create', "tournament_id" => $tournament->id], ['class' => 'btn btn-success']) ?>
	</p>

	<?
	$gridColumns = [
		[
			'class' => 'yii\grid\SerialColumn',
		],
		[
			'label'     => Yii::t("app", 'Average Panel Strength'),
			'attribute' => 'strength',
			'width'     => '100px',
		],
		[
			'class'              => '\kartik\grid\DataColumn',
			'attribute'          => 'adjudicators',
			'format'             => 'raw',
			'value'              => function ($model, $key, $index, $widget) {
				foreach ($model->getAdjudicatorsObjects() as $adj)
					$list[] = Html::a($adj->name, ["user/view", "id" => $adj->user->id]);

				return Html::ul($list,
					["encode" => false]);
			},
			'filterType'         => GridView::FILTER_SELECT2,
			'filter'             => \common\models\search\TeamSearch::getSpeakerSearchArray($tournament->id),
			'filterWidgetOptions' => [
				'pluginOptions' => ['allowClear' => true],
			],
			'filterInputOptions' => ['placeholder' => Yii::t("app", 'Any Speaker ...')],
		],
		[
			'class'       => '\kartik\grid\ActionColumn',
			'urlCreator'  => function ($action, $model, $key, $index) {
				return \yii\helpers\Url::to(["panel/" . $action, "id" => $model->id, "tournament_id" => $model->tournament->id]);
			},
			'viewOptions' => ['label' => \kartik\helpers\Html::icon("folder-open"), 'title' => Yii::t("app", 'View {modelClass}', ['modelClass' => 'Adjudicator']), 'data-toggle' => 'tooltip'],
			'updateOptions' => ['title' => Yii::t("app", 'Update {modelClass}', ['modelClass' => 'Adjudicator']), 'data-toggle' => 'tooltip'],
			'deleteOptions' => ['title' => Yii::t("app", 'Delete {modelClass}', ['modelClass' => 'Adjudicator']), 'data-toggle' => 'tooltip'],
			'width'       => '122px',
		],
	]
	?>

	<?
	echo GridView::widget([
		'dataProvider'    => $dataProvider,
		'filterModel'     => $searchModel,
		'columns'         => $gridColumns,
		'id'              => 'panels',
		'pjax'            => true,
		'showPageSummary' => false,
		'responsive'      => true,
		'hover'           => true,
		'floatHeader'     => true,
		'floatHeaderOptions' => ['scrollingTop' => 100],
	])
	?>
</div>
