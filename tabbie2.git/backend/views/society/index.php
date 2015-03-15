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
			'country.name:text:Country',
			[
				'class' => '\kartik\grid\DataColumn',
				'attribute' => 'country.region_id',
				'format' => 'raw',
				'value' => function ($model, $key, $index, $widget) {
					if (isset($model->country->region_id))
						return Country::getRegionLabel($model->country->region_id);
					else
						return Country::getRegionLabel(0);
				},
			],
			['class' => 'yii\grid\ActionColumn']
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
