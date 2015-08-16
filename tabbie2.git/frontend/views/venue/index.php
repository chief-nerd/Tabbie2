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
	<?
	$gridColumns = [
		[
			'class' => '\kartik\grid\SerialColumn',
		],
		[
			'class'     => 'kartik\grid\BooleanColumn',
			'attribute' => 'active',
			'vAlign'    => 'middle',
		],
		[
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'name',
		],
		[
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'group',
		],
		[
			'class'         => 'kartik\grid\ActionColumn',
			'width'         => "100px",
			'template'      => '{active}&nbsp;&nbsp;{view}&nbsp;&nbsp;{update}&nbsp;&nbsp;{delete}',
			'dropdown'      => false,
			'vAlign'        => 'middle',
			'buttons'       => [
				"active" => function ($url, $model) {
					return Html::a(\kartik\helpers\Html::icon("pause"), $url, [
						'title'              => Yii::t('app', 'Toogle Active'),
						'data-pjax' => '0',
						'data-toggle-active' => $model->id
					]);
				}
			],
			'urlCreator'    => function ($action, $model, $key, $index) {
				return \yii\helpers\Url::to(["venue/" . $action, "id" => $model->id, "tournament_id" => $model->tournament->id]);
			},
			'viewOptions'   => ['label' => \kartik\helpers\Html::icon("folder-open"), 'title' => Yii::t("app", 'View {modelClass}', ['modelClass' => 'Venue']), 'data-toggle' => 'tooltip'],
			'updateOptions' => ['title' => Yii::t("app", 'Update {modelClass}', ['modelClass' => 'Venue']), 'data-toggle' => 'tooltip'],
			'deleteOptions' => ['title' => Yii::t("app", 'Delete {modelClass}', ['modelClass' => 'Venue']), 'data-toggle' => 'tooltip'],
		],
	];

	if ($tournament->status < Tournament::STATUS_CLOSED) {
		$toolbar = [
			['content' =>
				 Html::a(\kartik\helpers\Html::icon("plus"), ['create', "tournament_id" => $tournament->id], [
					 'title'     => Yii::t('app', 'Add new element'),
					 'data-pjax' => 0,
					 'class'     => 'btn btn-default'
				 ]) .
				 Html::a(\kartik\helpers\Html::icon("repeat"), ['index', "tournament_id" => $tournament->id], [
					 'title'     => Yii::t('app', 'Reload content'),
					 'data-pjax' => 1,
					 'class'     => 'btn btn-default',
				 ]) .
				 Html::a(\kartik\helpers\Html::icon("import"), ['import', "tournament_id" => $tournament->id], [
					 'title'     => Yii::t('app', 'Import via CSV File'),
					 'data-pjax' => 0,
					 'class'     => 'btn btn-default'
				 ])
			],
			'{export}',
			'{toggleData}',
		];
	} else {
		$toolbar = [
			'{export}',
			'{toggleData}',
		];
	}


	echo GridView::widget([
		'dataProvider'       => $dataProvider,
		'columns'            => $gridColumns,
		'emptyText'          => Yii::t("app", "This tournament has no {object}s yet.<br>{add} a {object} or {import} them via csv File.", [
			"object" => "venue",
			"add"    => Html::a(\kartik\helpers\Html::icon("plus") . "&nbsp" . Yii::t('app', 'Add'), ['create', "tournament_id" => $tournament->id], ['data-pjax' => 0, 'class' => 'btn btn-success']),
			"import" => Html::a(\kartik\helpers\Html::icon("import") . "&nbsp" . Yii::t('app', 'Import'), ['import', "tournament_id" => $tournament->id], ['data-pjax' => 0, 'class' => 'btn btn-primary'])
		]),
		'id'                 => 'venues',
		'pjax'               => true,
		'pjaxSettings'       => [
			'options' => [
				'enablePushState' => false,
			]
		],
		'showPageSummary'    => false,
		'responsive'         => true,
		'hover'              => true,
		'floatHeader'        => true,
		'floatHeaderOptions' => ['scrollingTop' => '150'],
		'panel'              => [
			'type'    => GridView::TYPE_DEFAULT,
			'heading' => Html::encode($this->title),
			'before'  => "",
		],
		'toolbar'            => $toolbar,
	])
	?>
</div>
