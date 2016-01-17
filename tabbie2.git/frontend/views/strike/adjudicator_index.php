<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Strike Adjudicators');
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="strikes-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('app', 'Create Additional {modelClass}', [
			'modelClass' => 'Adjudicator Strikes',
		]), ['adjudicator_create', "tournament_id" => $tournament->id], ['class' => 'btn btn-success']) ?>

		<?
		if ($tournament->isTabMaster(Yii::$app->user->id) &&
				Yii::$app->user->model->role >= \common\models\User::ROLE_TABMASTER
		) {
			echo Html::a(Yii::t('app', 'Import Strikes'), ['import', "tournament_id" => $tournament->id], ['class' => 'btn btn-default']);
		}
		?>
	</p>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'class'     => 'kartik\grid\BooleanColumn',
				'attribute' => 'accepted',
				'vAlign'    => 'middle',
			],
			'adjudicatorFrom.name',
			'adjudicatorTo.name',

			[
				'class'      => 'kartik\grid\ActionColumn',
				'template'   => '{update}&nbsp;&nbsp;{delete}',
				'dropdown'   => false,
				'vAlign'     => 'middle',
				'urlCreator' => function ($action, $model, $key, $index) {
					return \yii\helpers\Url::to(["strike/adjudicator_" . $action, "adjudicator_from_id" => $model->adjudicator_from_id, "adjudicator_to_id" => $model->adjudicator_to_id, "tournament_id" => $model->tournament_id]);
				},
				'updateOptions' => ['title' => Yii::t("app", "Update team"), 'data-toggle' => 'tooltip'],
				'deleteOptions' => ['title' => Yii::t("app", "Delete team"), 'data-toggle' => 'tooltip'],
				'width'      => '100px',
			],
		],
	]); ?>

</div>
