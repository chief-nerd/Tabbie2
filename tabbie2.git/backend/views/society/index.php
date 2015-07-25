<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\models\Country;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\SocietySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Societies');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="society-index">
	<?
	$gridColumns = [
		['class' => 'yii\grid\SerialColumn'],
		'fullname',
		'abr',
		'city',
		[
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'country_name',
			'label'     => 'Country',
			'format'    => 'raw',
			'value'     => function ($model, $key, $index, $widget) {
				return $model->country->name;
			},
		],
		[
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'country_region',
			'label'     => 'UN Region',
			'format'    => 'raw',
			'value'     => function ($model, $key, $index, $widget) {
				if (isset($model->country->region_id))
					return Country::getRegionLabel($model->country->region_id);
				else
					return Country::getRegionLabel(0);
			},
		],
		[
			'class'         => '\kartik\grid\ActionColumn',
			'width'         => '120px',
			'vAlign'        => 'middle',
			'template'      => '{view}&nbsp;&nbsp;{update}&nbsp;&nbsp;{merge}&nbsp;&nbsp;{delete}',
			'buttons'       => [
				"merge" => function ($url, $model) {

					return $this->render("_merge", ["model" => $model]);
				},
			],
			'urlCreator'    => function ($action, $model, $key, $index) {
				return \yii\helpers\Url::to(["society/" . $action, "id" => $model->id]);
			},
			'viewOptions'   => ['label' => \kartik\helpers\Html::icon("folder-open"), 'title' => Yii::t("app", 'View {modelClass}', ['modelClass' => 'Society']), 'data-toggle' => 'tooltip'],
			'updateOptions' => ['title' => Yii::t("app", 'Update {modelClass}', ['modelClass' => 'Society']), 'data-toggle' => 'tooltip'],
			'deleteOptions' => ['title' => Yii::t("app", 'Delete {modelClass}', ['modelClass' => 'Society']), 'data-toggle' => 'tooltip'],
		]
	];

	$toolbar = [
		['content' =>
			 Html::a(\kartik\helpers\Html::icon("plus"), ['create'], [
				 'title'     => Yii::t('app', 'Add new element'),
				 'data-pjax' => 0,
				 'class'     => 'btn btn-default'
			 ]) .
			 Html::a(\kartik\helpers\Html::icon("repeat"), ['index'], [
				 'title'     => Yii::t('app', 'Reload content'),
				 'data-pjax' => 1,
				 'class'     => 'btn btn-default',
			 ]) .
			 Html::a(\kartik\helpers\Html::icon("import"), ['import'], [
				 'title'     => Yii::t('app', 'Import via CSV File'),
				 'data-pjax' => 0,
				 'class'     => 'btn btn-default'
			 ])
		],
		'{export}',
		'{toggleData}',
	];

	echo GridView::widget([
		'dataProvider'       => $dataProvider,
		'filterModel'        => $searchModel,
		'columns'            => $gridColumns,
		'id'                 => 'society',
		'pjax'               => true,
		'showPageSummary'    => false,
		'responsive'         => true,
		'hover'              => true,
		'floatHeader'        => true,
		'floatHeaderOptions' => ['scrollingTop' => 50],
		'panel'              => [
			'type'    => GridView::TYPE_DEFAULT,
			'heading' => Html::encode($this->title),
		],
		'toolbar'            => $toolbar,
	])
	?>

</div>
