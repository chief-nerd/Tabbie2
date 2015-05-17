<?php
use common\models\PublishTabSpeaker;
use yii\data\ArrayDataProvider;
use kartik\grid\GridView;
use yii\helpers\Html;

$lines = PublishTabSpeaker::generateSpeakerTab($model);

$dataProvider = new ArrayDataProvider([
	'allModels' => $lines,
	'sort' => [
		'attributes' => ['enl_place'],
	],
	'pagination' => [
		'pageSize' => 99999,
	],
]);
?>

<div class="tab-team-container">

	<h3><?= Html::encode(Yii::t("app", "Speaker Tab")) ?></h3>
	<?
	$columns = [
		[
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'enl_place',
			'label' => ($model->has_esl) ? Yii::t("app", 'ENL Place') : Yii::t("app", 'Place'),
			'width' => '100px',
		],
		[
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'esl_place',
			'label' => Yii::t("app", 'ESL Place'),
			'width' => '100px',
			'visible' => $model->has_esl,
			'value' => function ($model, $key, $index, $widget) {
				return ($model->esl_place) ? $model->esl_place : "";
			},
		],
		[
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'object.name',
			'label' => Yii::t("app", 'Speaker'),
			'format' => 'raw',
			'value' => function ($model, $key, $index, $widget) {
				return ($model->object) ? $model->object->name : "(not set)";
			},
		],
		[
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'points',
			'label' => Yii::t("app", 'Team Points'),
			'width' => "100px",
		],
		[
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'speaks',
			'label' => Yii::t("app", 'Speaker Points'),
			'width' => "120px",
		],
	];

	foreach ($model->rounds as $r) {
		$columns[] = [
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'results_array.' . $r->number,
			'label' => Yii::t("app", "#{number}", ["number" => $r->number]),
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
		'id' => 'team-speaker',
		'striped' => true,
	])
	?>

</div>
