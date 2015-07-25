<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Society */

$this->title = $model->fullname;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Societies'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="society-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger',
			'data'  => [
				'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
				'method'  => 'post',
			],
		]) ?>
	</p>

	<?= DetailView::widget([
		'model'      => $model,
		'attributes' => [
			'id',
			'abr',
			'city',
			'country.name:text:Country',
		],
	]) ?>


	<?
	$gridColumns = [
		['class' => 'yii\grid\SerialColumn'],
		[
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'name',
			'label'     => 'User',
			'format'    => 'raw',
			'value'     => function ($user, $key, $index, $widget) {
				return Html::a($user->name, Yii::$app->urlManagerFrontend->createUrl(["user/view", "id" => $user->id]));
			},
		],
		[
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'language_status',
		],
		[
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'inSocieties.starting',
			'label'     => 'Starting',
			'format'    => 'raw',
			'value'     => function ($user, $key, $index, $widget) {
				return $user->getInSocieties()
					->where(["society_id" => $this->context->actionParams["id"]])
					->one()
					->starting;
			},
		],
		[
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'inSocieties.ending',
			'label'     => 'Ending',
			'format'    => 'raw',
			'value'     => function ($user, $key, $index, $widget) {
				return $user->getInSocieties()
					->where(["society_id" => $this->context->actionParams["id"]])
					->one()
					->ending;
			},
		],
	];

	echo GridView::widget([
		'dataProvider'       => $memberDataProvider,
		'filterModel'        => $memberSearchModel,
		'columns'            => $gridColumns,
		'id'                 => 'member',
		'pjax'               => true,
		'showPageSummary'    => false,
		'responsive'         => true,
		'hover'              => true,
		'floatHeader'        => true,
		'floatHeaderOptions' => ['scrollingTop' => 50],

	])
	?>

</div>
