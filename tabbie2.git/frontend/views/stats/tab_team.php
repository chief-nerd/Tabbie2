<?php
use common\models\PublishTabTeam;
use yii\data\ArrayDataProvider;
use kartik\grid\GridView;
use yii\helpers\Html;

$lines = PublishTabTeam::generateTeamTab($model);

$dataProvider = new ArrayDataProvider([
	'allModels' => $lines,
	'sort'      => [
		'attributes' => ['enl_place'],
	],
	'pagination' => [
		'pageSize' => 99999,
	],
]);

?>
<div class="tab-team-container">

	<h3><?= Html::encode(Yii::t("app", "Team Tab")) ?></h3>
	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?
	$columns = [
		[
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'enl_place',
			'label' => ($model->has_esl) ? Yii::t("app", 'ENL Place') : Yii::t("app", 'Place'),
			'width' => '80px',
		],
		[
			'class'   => '\kartik\grid\DataColumn',
			'attribute' => 'esl_place',
			'label'   => Yii::t("app", 'ESL Place'),
			'width'   => '80px',
			'visible' => $model->has_esl,
			'value'   => function ($model, $key, $index, $widget) {
				return ($model->esl_place) ? $model->esl_place : "";
			},
		],
		[
			'class'  => '\kartik\grid\DataColumn',
			'attribute' => 'object.name',
			'label'  => Yii::t("app", 'Team'),
			'format' => 'raw',
			'value'  => function ($model, $key, $index, $widget) {
				return $model->object["name"];
			},
		],
		[
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'points',
			'label' => Yii::t("app", 'Team Points'),
			'width' => "20px",
		],
		[
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'speaks',
			'label' => Yii::t("app", 'Speaker Points'),
			'width' => "20px",
		],
	];

	foreach ($model->inrounds as $r) {
		$columns[] = [
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'results_array.' . $r->number,
			'label' => Yii::t("app", "#{number}", ["number" => $r->number]),
			'width' => "80px",
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
		'id'              => 'team-tab',
		'striped'         => false,
		'rowOptions'      => function ($model, $key, $index, $grid) {
			return ["class" => ($model->enl_place <= $this->context->_getContext()
					->getAmountBreakingTeams()) ? "bg-success" : ""];
		},
	])
	?>

</div>
<div class="clear"></div>
