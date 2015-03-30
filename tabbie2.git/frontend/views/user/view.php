<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->name;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">
	<h1>User Profile</h1>

	<div class="row">
		<div class="col-sm-8">
			<p>
				<? if (Yii::$app->user->id == Yii::$app->request->get("id") || Yii::$app->user->isAdmin()): ?>
					<?= Html::a(Yii::t('app', 'Update user profile'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
				<? endif; ?>
			</p>
			<?=
			DetailView::widget([
				'model' => $model,
				'attributes' => [
					'username',
					'name',
					'email:email',
				],
			])
			?>
		</div>
		<div class="col-xs-12 col-sm-4">
			<img class="img-rounded img-responsive center-block" src="<?= $model->getPicture() ?>"
			     style="max-height: 180px;">
		</div>
	</div>


	<h2>Debate Society History</h2>

	<p>
		<? if (Yii::$app->user->id == Yii::$app->request->get("id") || Yii::$app->user->isAdmin()): ?>
			<?= Html::a(Yii::t('app', 'Add new society to history'), ['society/create', 'user_id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<? endif; ?>
	</p>

	<?
	$gridColumns = [
		[
			'class' => '\kartik\grid\SerialColumn',
		],
		[
			'class' => 'kartik\grid\DataColumn',
			'attribute' => 'society.fullname',
			'vAlign' => 'middle',
		],
		[
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'starting',
		],
		[
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'ending',
			'format' => "raw",
			'value' => function ($model, $key, $index, $widget) {
				return ($model->ending) ? $model->ending : Yii::t("app", "still active");
			},
		],
		[
			'class' => 'kartik\grid\ActionColumn',
			'template' => '{update}',
			'dropdown' => false,
			'vAlign' => 'middle',
			'urlCreator' => function ($action, $model, $key, $index) {
				return \yii\helpers\Url::to(["society/" . $action, "user_id" => $model->user_id, "id" => $model->society_id]);
			},
			'updateOptions' => ['title' => Yii::t("app", "Update Society Info"), 'data-toggle' => 'tooltip'],
			'width' => '100px'
		],
	];

	echo GridView::widget([
		'dataProvider' => $dataSocietyProvider,
		'columns' => $gridColumns,
		'id' => 'venues',
		'pjax' => true,
		'showPageSummary' => false,
		'responsive' => true,
		'hover' => true,
		'floatHeader' => false,
		'floatHeaderOptions' => ['scrollingTop' => '150'],
	])
	?>


</div>
