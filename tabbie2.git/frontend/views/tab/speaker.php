<?php

use kartik\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\DrawSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Speaker Tab');
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tab-team-container">

	<h1><?= Html::encode($this->title) ?></h1>
	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?
	$columns = [
		[
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'enl_place',
			'label' => 'ENL Place',
			'width' => '80px',
		],
		[
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'esl_place',
			'label' => 'ESL Place',
			'width' => '80px',
			'visible' => $tournament->has_esl,
		],
		[
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'object.name',
			'label' => 'Speaker',
			'format' => 'raw',
			'value' => function ($model, $key, $index, $widget) {
				return Html::a($model->object->name, ["user/view", "id" => $model->object->id]);
			},
		],
		[
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'points',
			'label' => 'Team Points',
			'width' => "20px",
		],
		[
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'speaks',
			'label' => 'Speaker Points',
			'width' => "20px",
		],
	];

	foreach ($tournament->rounds as $r) {
		$columns[] = [
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'results_array.' . $r->number,
			'label' => "#" . $r->number,
			'width' => "80px",
		];
	}

	echo GridView::widget([
		'dataProvider' => $dataProvider,
		//'filterModel' => $searchModel,
		'columns' => $columns,
		'showPageSummary' => false,
		'layout' => "{items}\n{pager}",
		'bootstrap' => true,
		'pjax' => false,
		'hover' => true,
		'responsive' => false,
		'floatHeader' => true,
		'floatHeaderOptions' => ['scrollingTop' => 100],
		'id' => 'team-tab',
		'striped' => true,
	])
	?>

</div>
