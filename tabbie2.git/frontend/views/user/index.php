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

	<p>
		<?= Html::a(Yii::t('app', 'Create {modelClass}', [
			'modelClass' => 'User',
		]), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?
	$gridColumns = [
		[
			'class' => '\kartik\grid\SerialColumn',
		],
		[
			'class' => 'kartik\grid\DataColumn',
			'attribute' => 'url_slug',
			'vAlign' => 'middle',
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
			'class' => '\kartik\grid\DataColumn',
			'attribute' => 'role',
			'format' => "raw",
			'value' => function ($model, $key, $index, $widget) {
				return \common\models\User::getRoleLabel($model->role);
			},
		],
		[
			'class' => 'kartik\grid\ActionColumn',
			'width' => "100px",
			'template' => '{forcepass}&nbsp;&nbsp;{view}&nbsp;&nbsp;{update}&nbsp;&nbsp;{delete}',
			'dropdown' => false,
			'vAlign' => 'middle',
			'buttons' => [
				"forcepass" => function ($url, $model) {
					return Html::a("<span class='glyphicon glyphicon-lock'></span>", $url, [
						'title' => Yii::t('app', 'Set new Password'),
						'data-pjax' => '0',
						'data-toggle-active' => $model->id
					]);
				}
			],
			'urlCreator' => function ($action, $model, $key, $index) {
				return \yii\helpers\Url::to(["user/" . $action, "id" => $model->id]);
			},
			'viewOptions' => ['label' => '<i class="glyphicon glyphicon-folder-open"></i>', 'title' => Yii::t("app", "View User"), 'data-toggle' => 'tooltip'],
			'updateOptions' => ['title' => Yii::t("app", "Update User"), 'data-toggle' => 'tooltip'],
			'deleteOptions' => ['title' => Yii::t("app", "Delete User"), 'data-toggle' => 'tooltip'],
			'width' => '100px'
		],
	];

	echo GridView::widget([
		'dataProvider' => $dataProvider,
		'columns' => $gridColumns,
		'id' => 'users',
		'pjax' => true,
		'showPageSummary' => false,
		'responsive' => true,
		'hover' => true,
		'floatHeader' => false,
		'floatHeaderOptions' => ['scrollingTop' => '100'],
	])
	?>

</div>
