<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Energy Configs');
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="energy-config-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<?=
	GridView::widget([
		'dataProvider' => $dataProvider,
		'columns' => [
			'label',
			'value',
			[
				'class' => 'kartik\grid\ActionColumn',
				'template' => '{update}',
				'urlCreator' => function ($action, $model, $key, $index) {
					return \yii\helpers\Url::to(["energy/" . $action, "id" => $model->id, "tournament_id" => $model->tournament->id]);
				},
				'updateOptions' => ['title' => Yii::t("app", "Update Energy Value"), 'data-toggle' => 'tooltip'],
			],
		],
	]);
	?>

</div>
