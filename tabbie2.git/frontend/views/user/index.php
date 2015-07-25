<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

	<h1><?= Html::encode($this->title) ?></h1>
	<?php echo $this->render('_search', ['model' => $searchModel]); ?>

	<?
	$gridColumns = [
		[
			'class' => 'kartik\grid\DataColumn',
			'attribute' => 'id',
		],
		[
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'picture',
			'format'    => "raw",
			'value'     => function ($model, $key, $index, $widget) {
				/** @var $model \common\models\User */
				return $model->getPictureImage(30, 30);
			},
		],
		[
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'name',
		],
		[
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'email',
		],
		[
			'class'  => '\kartik\grid\DataColumn',
			'attribute' => 'role',
			'format' => "raw",
			'value'  => function ($model, $key, $index, $widget) {
				return \common\models\User::getRoleLabel($model->role);
			},
		],
		[
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'last_change',
		],
		[
			'class'       => 'kartik\grid\ActionColumn',
			'template'    => '{forcepass}&nbsp;&nbsp;{view}&nbsp;&nbsp;{update}&nbsp;&nbsp;{delete}',
			'dropdown'    => false,
			'vAlign'      => 'middle',
			'buttons'     => [
				"forcepass" => function ($url, $model) {
					return Html::a("<span class='glyphicon glyphicon-lock'></span>", $url, [
						'title'     => Yii::t('app', 'Set new Password'),
						'data-pjax' => '0',
						'data-toggle-active' => $model->id
					]);
				}
			],
			'urlCreator'  => function ($action, $model, $key, $index) {
				return \yii\helpers\Url::to(["user/" . $action, "id" => $model->id]);
			},
			'viewOptions' => ['label' => '<i class="glyphicon glyphicon-folder-open"></i>', 'title' => Yii::t("app", "View User"), 'data-toggle' => 'tooltip'],
			'updateOptions' => ['title' => Yii::t("app", "Update User"), 'data-toggle' => 'tooltip'],
			'deleteOptions' => ['title' => Yii::t("app", "Delete User"), 'data-toggle' => 'tooltip'],
			'width'       => '120px'
		],
	];

	$toolbar = [
		['content' =>
			 Html::a(\kartik\helpers\Html::icon("plus"), ['create'], [
				 'title'     => Yii::t('app', 'Add new element'),
				 'data-pjax' => 0,
				 'class'     => 'btn btn-default'
			 ]) .
			 Html::a(\kartik\helpers\Html::icon("repeat"), ['index'], [
				 'data-pjax' => 1,
				 'class'     => 'btn btn-default',
				 'title'     => Yii::t('app', 'Reload content'),
			 ])
		],
		'{export}',
		'{toggleData}',
	];

	echo GridView::widget([
		'dataProvider'    => $dataProvider,
		'columns'         => $gridColumns,
		'id'              => 'users',
		'pjax'            => true,
		'showPageSummary' => false,
		'responsive'      => true,
		'hover'           => true,
		'floatHeader'     => false,
		'floatHeaderOptions' => ['scrollingTop' => '100'],
		'panel'           => [
			'type'    => GridView::TYPE_DEFAULT,
			'heading' => Html::encode($this->title),
		],
		'toolbar'         => $toolbar,
	])
	?>

</div>
