<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];

$this->title = Yii::t('app', 'Language Officers');

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="language-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?=
		Html::a(Yii::t('app', 'Add Officer'), ['officer-add', "tournament_id" => $tournament->id], ['class' => 'btn btn-success'])
		?>
	</p>

	<?
	$gridColumns = [
		[
			'class' => '\kartik\grid\SerialColumn',
		],
		[
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'user.name',
			'filterType' => GridView::FILTER_SELECT2,
			'filter' => \common\models\search\UserSearch::getSearchTournamentArray($tournament->id),
			'filterWidgetOptions' => [
				'pluginOptions' => ['allowClear' => true],
			],
			'filterInputOptions' => ['placeholder' => Yii::t("app", 'Any User ...')],
		],
		[
			'class' => 'kartik\grid\ActionColumn',
			'template' => '{delete}',
			'urlCreator' => function ($action, $model, $key, $index) {
				return \yii\helpers\Url::to(["language/officer-" . $action, "id" => $model->user_id, "tournament_id" => $this->context->_getContext()->id]);
			},
			'vAlign' => 'middle',
			'width' => '120px',
		],
	];

	echo GridView::widget([
		'dataProvider' => $dataProvider,
		//'filterModel' => $searchModel,
		'columns' => $gridColumns,
		'id' => 'language',
		'pjax' => true,
		'showPageSummary' => false,
		'responsive' => false,
		'hover' => true,
		'floatHeader' => true,
		'floatHeaderOptions' => ['scrollingTop' => 100],

	])
	?>

</div>
