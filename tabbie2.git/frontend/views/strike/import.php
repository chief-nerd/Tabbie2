<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Import Strikes');
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="strikes-index">

	<h1><?= Html::encode($this->title) ?></h1>


	<?= GridView::widget([
		'dataProvider'       => $dataProvider,
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
		'columns'            => [
			['class' => 'yii\grid\SerialColumn'],
			'user.name',
			'clashWith.name',
			'reason',
			[
				'class'     => '\kartik\grid\DataColumn',
				'attribute' => 'date',
			],
			[
				'class'     => '\kartik\grid\DataColumn',
				'attribute' => 'type',
				'format'    => "raw",
				'value'     => function ($model, $key, $index, $widget) {
					/** @var $model \common\models\User */
					$tournament = $this->context->_getContext();

					return $model->getTypeLabel($tournament->id);
				},
			],
			[
				'class'      => 'kartik\grid\ActionColumn',
				'buttons'    => [
					"accept" => function ($url, $model) {
						return Html::a(\kartik\helpers\Html::icon("ok-sign"), $url, [
							'title'              => Yii::t('app', 'Accept'),
							'data-pjax'          => '1',
							'data-toggle-active' => $model->id,
							'class'              => 'btn btn-success'
						]);
					},
					"deny"   => function ($url, $model) {
						return Html::a(\kartik\helpers\Html::icon("remove-sign"), $url, [
							'title'              => Yii::t('app', 'Deny'),
							'data-pjax'          => '1',
							'data-toggle-active' => $model->id,
							'class'              => 'btn btn-danger'
						]);
					}
				],
				'template'   => '{accept}&nbsp;&nbsp;{deny}',
				'dropdown'   => false,
				'vAlign'     => 'middle',
				'urlCreator' => function ($action, $model, $key, $index) {
					$tournament = $this->context->_getContext();

					return \yii\helpers\Url::to(["strike/import", "clash" => $model->id, "action" => $action, "tournament_id" => $tournament->id]);
				},
				'width'      => '150px',
			],
		],
	]); ?>

</div>
