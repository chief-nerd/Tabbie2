<?php

use kartik\grid\GridView;
use kartik\export\ExportMenu;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\DrawSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Team Tab');
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tab-team-container">
	<?
	$columns = [
		[
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'enl_place',
			'label' => ($tournament->has_esl) ? Yii::t("app", 'ENL Place') : Yii::t("app", 'Place'),
			'width' => '80px',
		],
		[
			'class'   => '\kartik\grid\DataColumn',
			'attribute' => 'esl_place',
			'label'   => Yii::t("app", 'ESL Place'),
			'width'   => '80px',
			'visible' => $tournament->has_esl,
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
				return Html::a($model->object->name, ["team/view", "id" => $model->object->id, "tournament_id" => $model->object->tournament_id]);
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

	foreach ($tournament->rounds as $r) {
		$columns[] = [
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'results_array.' . $r->number,
			'label' => Yii::t("app", "#{number}", ["number" => $r->number]),
			'width' => "80px",
		];
	}
	?>
	<div class="row">
		<div class="col-xs-10 col-sm-6"><h2><?= Html::encode($this->title) ?></h2></div>
		<div class="col-xs-2 col-sm-6 text-right">
			<? echo ExportMenu::widget([
				'dataProvider'    => $dataProvider,
				'columns'         => $columns,
				'fontAwesome'     => true,
				'target'          => ExportMenu::TARGET_BLANK,
				'dropdownOptions' => [
					'label'       => 'Export',
					'class'       => 'btn btn-default',
					'itemsBefore' => [
						'<li class="dropdown-header">Export All Data</li>',
					],
				]
			]);
			?>
		</div>
	</div>

	<?
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
		'floatHeaderOptions' => ['scrollingTop' => 100],
		'id'              => 'team-tab',
		'striped'         => false,
		'emptyCell'       => "a",
		'rowOptions'      => function ($model, $key, $index, $grid) {
			return ["class" => ($model->enl_place <= $this->context->_getContext()
					->getAmountBreakingTeams()) ? "bg-success" : ""];
		},
	])
	?>

</div>
