<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use common\models\Adjudicator;
use common\models\Tournament;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\AdjudicatorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Adjudicators');
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="adjudicator-index">
	<?
	$gridColumns = [
		[
			'class' => '\kartik\grid\SerialColumn',
		],
		[
			'class'     => 'kartik\grid\BooleanColumn',
			'attribute' => 'active',
			'vAlign'    => 'middle',
			'width' => '50px',
		],
		[
			'class'     => '\kartik\grid\BooleanColumn',
			'attribute' => 'can_chair',
			'label'      => Yii::t("app", "Chair"),
			'trueLabel'  => Yii::t("app", "Can chair"),
			'falseLabel' => Yii::t("app", "Should not chair"),
			'width'      => '50px',
			'vAlign' => 'middle',
		],
		[
			'class'      => 'kartik\grid\BooleanColumn',
			'attribute'  => 'breaking',
			'label'      => Yii::t("app", "Break"),
			'vAlign'     => 'middle',
			'trueIcon'   => \kartik\helpers\Html::icon("star", ['class' => 'text-warning']),
			'trueLabel'  => Yii::t("app", "Breaking"),
            'falseLabel' => Yii::t("app", "Not breaking"),
			'falseIcon'  => "&nbsp;",
			'width'      => '50px',
		],
		[
			'class'               => '\kartik\grid\DataColumn',
			'attribute'           => 'name',
			'filterType'          => GridView::FILTER_SELECT2,
			'filter'              => \common\models\search\AdjudicatorSearch::getSearchArray($tournament->id),
			'filterWidgetOptions' => [
				'pluginOptions' => ['allowClear' => true],
			],
			'filterInputOptions' => ['placeholder' => Yii::t("app", 'Any {object} ...', ['object' => Yii::t("app", 'Adjudicator')])],
		],
		[
			'class'               => '\kartik\grid\DataColumn',
			'attribute'           => 'societyName',
			'filterType'          => GridView::FILTER_SELECT2,
			'filter'              => \common\models\search\SocietySearch::getTournamentSearchArray($tournament->id),
			'filterWidgetOptions' => [
				'pluginOptions' => ['allowClear' => true],
			],
			'filterInputOptions' => ['placeholder' => Yii::t("app", 'Any {object} ...', ['object' => Yii::t("app", 'Society')])],
			'width'               => '20%',
		],
		[
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'strength',
			'format'    => "raw",
			'value'     => function ($model, $key, $index, $widget) {
				return $model->getStrengthOutput();
			},
			'width'     => '15%',
		],
		[
			'class'     => '\kartik\grid\BooleanColumn',
			'attribute' => 'are_watched',
			'trueIcon'  => \kartik\helpers\Html::icon("eye-open", ['class' => 'text-success']),
			'falseIcon' => \kartik\helpers\Html::icon("eye-close", ['class' => 'text-muted']),
			'vAlign'    => 'middle',
			'width'     => '5%',
			'trueLabel'  => Yii::t("app", "Watched"),
			'falseLabel' => Yii::t("app", "Unwatched"),
		],
		[
			'class'         => 'kartik\grid\ActionColumn',
			'template'      => '{active}&nbsp{break}&nbsp;{watch}&nbsp;{view}&nbsp;{update}&nbsp;{delete}',
			'dropdown'      => false,
			'vAlign'        => 'middle',
			'buttons'       => [
				"watch"  => function ($url, $model) {
					if ($model->are_watched == 1) $icon = "eye-close";
					else $icon = "eye-open";

					return Html::a(\kartik\helpers\Html::icon($icon), $url, [
						'title'              => Yii::t('app', 'Toogle Watch'),
						'data-pjax' => '1',
						'data-toggle-active' => $model->id
					]);
				},
				"active" => function ($url, $model) {
					if ($model->active == 1) $icon = "pause";
					else $icon = "play";

					return Html::a(\kartik\helpers\Html::icon($icon), $url, [
						'title'              => Yii::t('app', 'Toogle Active'),
						'data-pjax' => '1',
						'data-toggle-active' => $model->id
					]);
				},
				"break"  => function ($url, $model) {
					if ($model->breaking == 1) $icon = "star-empty";
					else $icon = "star";

					return Html::a(\kartik\helpers\Html::icon($icon), $url, [
						'title'     => Yii::t('app', 'Toogle Breaking'),
						'data-pjax' => '1',
						'data-toggle-active' => $model->id
					]);
				}
			],
			'urlCreator'    => function ($action, $model, $key, $index) {
				return \yii\helpers\Url::to(["adjudicator/" . $action, "id" => $model->id, "tournament_id" => $model->tournament->id]);
			},
			'viewOptions'   => ['label' => \kartik\helpers\Html::icon("folder-open"), 'title' => Yii::t("app", 'View {modelClass}', ['modelClass' => 'Adjudicator']), 'data-toggle' => 'tooltip'],
			'updateOptions' => ['title' => Yii::t("app", 'Update {modelClass}', ['modelClass' => 'Adjudicator']), 'data-toggle' => 'tooltip'],
			'deleteOptions' => ['title' => Yii::t("app", 'Delete {modelClass}', ['modelClass' => 'Adjudicator']), 'data-toggle' => 'tooltip'],
			'width' => '130px',
		],
	];

	$average = ($stat["venues"] != 0) ? $stat["amount"] / $stat["venues"] : 0;
	$stats = '<table class="col-xs-12 col-md-4">
                <tr>
                    <th>Average Adjudicator per Room</th>
                    <th>Active</th>
                    <th>Inactive</th>
                </tr>
                <tr>
                    <td><b class="' . (($average >= 2) ? "text-success" : "text-danger") . '">' . Yii::$app->formatter->asDecimal($average, 1) . '</b></td>
                    <td>' . $stat["amount"] . '</td>
                    <td>' . $stat["inactive"] . '</td>
                </tr>
            </table>';

	if ($tournament->status < Tournament::STATUS_CLOSED) {
		$toolbar = [
			['content' =>
				 Html::a(\kartik\helpers\Html::icon("plus"), ['create', "tournament_id" => $tournament->id], [
					 'title' => Yii::t('app', 'Add {object}', ['object' => Yii::t("app", 'Adjudicator')]),
					 'data-pjax' => 0,
					 'class'     => 'btn btn-default'
				 ]) .
				 Html::a(\kartik\helpers\Html::icon("repeat"), ['index', "tournament_id" => $tournament->id], [
					 'data-pjax' => 1,
					 'class'     => 'btn btn-default',
					 'title'     => Yii::t('app', 'Reload content'),
				 ]) .
				 Html::a(\kartik\helpers\Html::icon("import"), ['import', "tournament_id" => $tournament->id], [
					 'data-pjax' => 0,
					 'class'     => 'btn btn-default',
					 'title'     => Yii::t('app', 'Import via CSV File'),
				 ])
			],
			[
				'content' => Html::a(\kartik\helpers\Html::icon("eye-close") . "&nbsp;" . Yii::t('app', 'Reset'), [
					'resetwatched', "tournament_id" => $tournament->id
				], [
					'class' => 'btn btn-default',
					'title' => Yii::t('app', 'Reset watcher flag'),
				]),
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
		'filterModel'        => $searchModel,
		'filterUrl' => \yii\helpers\Url::to(["adjudicator/index", "tournament_id" => $tournament->id]),
		'columns'            => $gridColumns,
        'emptyText' => Yii::t("app", "<b>This tournament has no adjudicators yet.</b><br>{add_button} or {import_button}.", [
                "add_button"    => Html::a(\kartik\helpers\Html::icon("plus") . "&nbsp" . Yii::t('app', 'Add'), ['Add an adjudicator', "tournament_id" => $tournament->id], ['data-pjax' => 0, 'class' => 'btn btn-success']),
                "import_button" => Html::a(\kartik\helpers\Html::icon("import") . "&nbsp" . Yii::t('app', 'Import them via CSV File'), ['import', "tournament_id" => $tournament->id], ['data-pjax' => 0, 'class' => 'btn btn-primary'])
		]),
		'id'                 => 'adjudicators',
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
		'floatHeaderOptions' => ['scrollingTop' => 100],
		'panel'              => [
			'type'    => GridView::TYPE_DEFAULT,
			'heading' => Html::encode($this->title),
			'before'  => $stats,
		],
		'toolbar'            => $toolbar,
	])
	?>
</div>
