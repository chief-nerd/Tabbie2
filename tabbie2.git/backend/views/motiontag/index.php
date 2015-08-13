<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\MotionTagSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Motion Tags');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="motion-tag-index">

	<h1><?= Html::encode($this->title) ?></h1>
	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<p>
		<?= Html::a(Yii::t('app', 'Create Motion Tag'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel'  => $searchModel,
		'columns'      => [
			['class' => 'yii\grid\SerialColumn'],
			'name',
			'abr',
			'count',
			[
				'class'    => 'kartik\grid\ActionColumn',
				'width'    => '120px',
				'template' => '{view}&nbsp;&nbsp;{update}&nbsp;&nbsp;{merge}&nbsp;&nbsp;{delete}',
				'buttons'  => [
					"merge" => function ($url, $model) {

						return $this->render("_merge", ["model" => $model]);
					},
				],
			],
		],
	]); ?>

</div>
