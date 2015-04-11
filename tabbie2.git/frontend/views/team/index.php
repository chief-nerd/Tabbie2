<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use common\models\Tournament;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\TeamSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Teams');
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="team-index">

	<?php \yii\widgets\Pjax::begin(); ?>
	<div class="row">
		<div class="col-md-8">
			<h1><?= Html::encode($this->title) ?></h1>

			<p>
				<? if ($tournament->status < Tournament::STATUS_CLOSED): ?>
				<?=
					Html::a(\kartik\helpers\Html::icon("plus") . "&nbsp" . Yii::t('app', 'Create {modelClass}', [
					'modelClass' => 'Team',
				]), ['create', "tournament_id" => $tournament->id], ['class' => 'btn btn-success'])
				?>
				<?=
					Html::a(\kartik\helpers\Html::icon("import") . "&nbsp" . Yii::t('app', 'Import {modelClass}', [
					'modelClass' => 'Team',
				]), ['import', "tournament_id" => $tournament->id], ['class' => 'btn btn-default'])
				?>
				<? endif; ?>
			</p>
		</div>
		<div class="col-md-4">
			<table class="table">
				<tr>
					<th>Missing Teams</th>
					<th>Inactive Teams</th>
					<th>Swing Teams</th>
				</tr>
				<tr>
					<td><?
						$modolo = $stat["active"] % 4;
						$missing = ($modolo == 0) ? 0 : 4 - $modolo;

						echo "<b class='" . (($modolo == 0) ? "text-success" : "text-danger") . "'>" . $missing . "</b>";
						?></td>
					<td><?= $stat["inactive"] ?></td>
					<td><?= $stat["swing"] ?></td>
				</tr>
			</table>
		</div>
	</div>
	<?
	$gridColumns = [
		[
			'class' => '\kartik\grid\SerialColumn',
		],
		[
			'class' => 'kartik\grid\BooleanColumn',
			'attribute' => 'active',
			'vAlign' => 'middle',
		],
		[
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'name',
			'format' => 'raw',
			'vAlign' => GridView::ALIGN_MIDDLE,
			'value' => function ($model, $key, $index, $widget) {
				return Html::a($model->name, ["view", "id" => $model->id, "tournament_id" => $model->tournament->id]);
			},
			'filterType' => GridView::FILTER_SELECT2,
			'filter' => \common\models\search\TeamSearch::getSearchArray($tournament->id),
			'filterWidgetOptions' => [
				'pluginOptions' => ['allowClear' => true],
			],
			'filterInputOptions' => ['placeholder' => Yii::t("app", 'Any Team ...')],
		],
		[
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'societyName',
			'vAlign' => GridView::ALIGN_MIDDLE,
			'filter' => \common\models\search\SocietySearch::getSearchArray($tournament->id),
			'filterType' => GridView::FILTER_SELECT2,
			'filterWidgetOptions' => [
				'pluginOptions' => ['allowClear' => true],
			],
			'filterInputOptions' => ['placeholder' => Yii::t("app", 'Any Society ...')],
		],
		[
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'speakerName',
			'format' => 'raw',
			'value' => function ($model, $key, $index, $widget) {
				return Html::ul([
					Html::a($model->speakerA->name, ["user/view", "id" => $model->speakerA->id]),
					Html::a($model->speakerB->name, ["user/view", "id" => $model->speakerB->id])], ["encode" => false]);
			},
			'filterType' => GridView::FILTER_SELECT2,
			'filter' => \common\models\search\TeamSearch::getSpeakerSearchArray($tournament->id),
			'filterWidgetOptions' => [
				'pluginOptions' => ['allowClear' => true],
			],
			'filterInputOptions' => ['placeholder' => Yii::t("app", 'Any Speaker ...')],
		],
		[
			'class' => 'kartik\grid\ActionColumn',
			'template' => '{active}&nbsp;&nbsp;{view}&nbsp;&nbsp;{update}&nbsp;&nbsp;{delete}',
			'dropdown' => false,
			'vAlign' => 'middle',
			'buttons' => [
				"active" => function ($url, $model) {
					return Html::a(\kartik\helpers\Html::icon("pause"), $url, [
						'title' => Yii::t('app', 'Toogle Active'),
						'data-pjax' => '0',
						'data-toggle-active' => $model->id
					]);
				}
			],
			'urlCreator' => function ($action, $model, $key, $index) {
				return \yii\helpers\Url::to(["team/" . $action, "id" => $model->id, "tournament_id" => $model->tournament->id]);
			},
			'viewOptions' => ['label' => \kartik\helpers\Html::icon("folder-open"), 'title' => Yii::t("app", 'View {modelClass}', ['modelClass' => 'Team']), 'data-toggle' => 'tooltip'],
			'updateOptions' => ['title' => Yii::t("app", 'Update {modelClass}', ['modelClass' => 'Team']), 'data-toggle' => 'tooltip'],
			'deleteOptions' => ['title' => Yii::t("app", 'Delete {modelClass}', ['modelClass' => 'Team']), 'data-toggle' => 'tooltip'],
			'width' => '100px',
		],
	];

	echo GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => $gridColumns,
		'id' => 'teams',
		'pjax' => true,
		'showPageSummary' => false,
		'responsive' => true,
		'hover' => true,
		'floatHeader' => true,
		'floatHeaderOptions' => ['scrollingTop' => 100],
	])
	?>
	<?php \yii\widgets\Pjax::end(); ?>

</div>
