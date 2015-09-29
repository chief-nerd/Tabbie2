<?php
use common\models\PublishTabSpeaker;
use yii\data\ArrayDataProvider;
use kartik\grid\GridView;
use yii\helpers\Html;

?>

<div class="tab-team-container">

	<h3><?= Html::encode(Yii::t("app", "Speaker Tab")) ?></h3>
	<?
	$columns = [
		[
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'enl_place',
			'label' => ($model->has_esl) ? Yii::t("app", 'ENL') : Yii::t("app", 'Place'),
			'width' => '50px',
		],
		[
			'class'   => '\kartik\grid\DataColumn',
			'attribute' => 'esl_place',
			'label' => Yii::t("app", 'ESL'),
			'width' => '50px',
			'visible' => $model->has_esl,
			'value'   => function ($model, $key, $index, $widget) {
				return ($model->esl_place) ? $model->esl_place : "";
			},
		],
		[
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'efl_place',
			'label' => Yii::t("app", 'EFL'),
			'width' => '50px',
			'visible' => $model->has_efl,
			'value' => function ($model, $key, $index, $widget) {
				return ($model->efl_place) ? $model->efl_place : "";
			},
		],
		[
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'novice_place',
			'label' => Yii::t("app", 'Novice'),
			'width' => '50px',
			'visible' => $model->has_novice,
			'value' => function ($model, $key, $index, $widget) {
				return ($model->novice_place) ? $model->novice_place : "";
			},
		],
		[
			'class'  => '\kartik\grid\DataColumn',
			'attribute' => 'object.speaker.name',
			'label'  => Yii::t("app", 'Speaker'),
			'format' => 'raw',
			'value'  => function ($model, $key, $index, $widget) {
				return ($model->object) ? $model->object["speaker"]["name"] : "(not set)";
			},
		],
		[
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'team.name',
			'label'     => Yii::t("app", 'Team'),
			'format'    => 'raw',
			'value'     => function ($model, $key, $index, $widget) {
				return Html::a($model->object["team"]["name"], ["team/view", "id" => $model->object["team"]["id"], "tournament_id" => $model->object["team"]["tournament_id"]]);
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

	foreach ($model->inrounds as $r) {
		$columns[] = [
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'results_array.' . $r->number,
			'label' => Yii::t("app", "#{number}", ["number" => $r->number]),
			'width' => "40px",
		];
	}

	echo GridView::widget([
		'dataProvider'    => $dataProvider,
		//'filterModel' => $searchModel,
		'columns'         => $columns,
		'showPageSummary' => false,
		'layout'          => "{items}\n{pager}",
		'bootstrap'       => true,
		'pjax'            => false,
		'hover'           => true,
		'responsive'      => false,
		'floatHeader'     => true,
		'floatHeaderOptions' => ['scrollingTop' => ($model->isTabMaster(Yii::$app->user->id) ? 100 : 50)],
		'id'              => 'team-speaker',
		'striped'         => true,
	])
	?>

</div>
<div class="clear"></div>
