<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\FeedbackSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Adjudicator Feedback');
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="feedback-index">

	<h1><?= Html::encode($this->title) ?></h1>
	<?php echo $this->render('_search_adjudicator', ['model' => $searchModel, "tournament" => $tournament]); ?>

	<?
	$gridColumns = [
		[
			'class' => '\kartik\grid\SerialColumn'
		],
		'question.text',
		'feedback.debate.round.number',
		'value',
		[
			'class' => 'kartik\grid\ActionColumn',
			'width' => "100px",
			'template' => '{view}&nbsp;&nbsp;{update}',
			'dropdown' => false,
			'vAlign' => 'middle',
			'urlCreator' => function ($action, $model, $key, $index) {
				return \yii\helpers\Url::to(["feedback/" . $action, "id" => $model->id]);
			},
			'viewOptions' => ['label' => '<i class="glyphicon glyphicon-folder-open"></i>', 'title' => Yii::t("app", 'View {modelClass}', ['modelClass' => 'Team']), 'data-toggle' => 'tooltip'],
			'updateOptions' => ['title' => Yii::t("app", 'Update {modelClass}', ['modelClass' => 'Team']), 'data-toggle' => 'tooltip'],
			'width' => '100px'
		]
	];

	echo GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => $gridColumns,
		'id' => 'feedback',
		'pjax' => true,
		'showPageSummary' => false,
		'responsive' => true,
		'hover' => true,
		'floatHeader' => false,
		'floatHeaderOptions' => ['scrollingTop' => '150'],
	])
	?>

</div>
