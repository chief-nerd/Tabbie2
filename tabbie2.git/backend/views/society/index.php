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

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?=
			Html::a(Yii::t('app', 'Create {modelClass}', [
				'modelClass' => 'Society',
			]), ['create'], ['class' => 'btn btn-success'])
		?>
	</p>

	<?
		$gridColumns = [
			['class' => 'yii\grid\SerialColumn'],
			'fullname',
			'abr',
			'city',
			[
				'class' => '\kartik\grid\DataColumn',
				'attribute' => 'country_name',
				'label' => 'Country',
				'format' => 'raw',
				'value' => function ($model, $key, $index, $widget) {
					return $model->country->name;
				},
			],
			[
				'class' => '\kartik\grid\DataColumn',
				'attribute' => 'country_region',
				'label' => 'UN Region',
				'format' => 'raw',
				'value' => function ($model, $key, $index, $widget) {
					if (isset($model->country->region_id))
						return Country::getRegionLabel($model->country->region_id);
					else
						return Country::getRegionLabel(0);
				},
			],
			[
				'class' => '\kartik\grid\ActionColumn',
				'width' => '120px',
				'vAlign' => 'middle',
				'template' => '{view}&nbsp;&nbsp;{update}&nbsp;&nbsp;{merge}&nbsp;&nbsp;{delete}',
				'buttons' => [
					"merge" => function ($url, $model) {

						return $this->render("_merge", ["model" => $model]);
					},
				],
				'urlCreator' => function ($action, $model, $key, $index) {
					return \yii\helpers\Url::to(["society/" . $action, "id" => $model->id]);
				},
				'viewOptions' => ['label' => \kartik\helpers\Html::icon("folder-open"), 'title' => Yii::t("app", 'View {modelClass}', ['modelClass' => 'Society']), 'data-toggle' => 'tooltip'],
				'updateOptions' => ['title' => Yii::t("app", 'Update {modelClass}', ['modelClass' => 'Society']), 'data-toggle' => 'tooltip'],
				'deleteOptions' => ['title' => Yii::t("app", 'Delete {modelClass}', ['modelClass' => 'Society']), 'data-toggle' => 'tooltip'],
			]
		];

		echo GridView::widget([
			'dataProvider' => $dataProvider,
			'filterModel' => $searchModel,
			'columns' => $gridColumns,
			'id' => 'society',
			'pjax' => true,
			'showPageSummary' => false,
			'responsive' => true,
			'hover' => true,
			'floatHeader' => true,
			'floatHeaderOptions' => ['scrollingTop' => 50],

		])
	?>

</div>
