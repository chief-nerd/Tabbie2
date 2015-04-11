<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Strike Team with Adjudicator');
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="strikes-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('app', 'Create Additional {modelClass}', [
			'modelClass' => 'Team Strikes',
		]), ['team_create', "tournament_id" => $tournament->id], ['class' => 'btn btn-success']) ?>
	</p>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],

			'team.name',
			'adjudicator.name',

			[
				'class' => 'kartik\grid\ActionColumn',
				'template' => '{update}&nbsp;&nbsp;{delete}',
				'dropdown' => false,
				'vAlign' => 'middle',
				'urlCreator' => function ($action, $model, $key, $index) {
					return \yii\helpers\Url::to(["strike/team_" . $action, "team_id" => $model->team_id, "adjudicator_id" => $model->adjudicator_id, "tournament_id" => $model->tournament_id]);
				},
				'updateOptions' => ['title' => Yii::t("app", "Update Team"), 'data-toggle' => 'tooltip'],
				'deleteOptions' => ['title' => Yii::t("app", "Delete Team"), 'data-toggle' => 'tooltip'],
				'width' => '100px',
			],
		],
	]); ?>

</div>
