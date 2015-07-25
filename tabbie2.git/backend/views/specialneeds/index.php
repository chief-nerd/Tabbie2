<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Special Needs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="special-needs-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('app', 'Create {modelClass}', [
			'modelClass' => 'Special Needs',
		]), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?= ListView::widget([
		'dataProvider' => $dataProvider,
		'itemOptions'  => ['class' => 'item'],
		'itemView'     => function ($model, $key, $index, $widget) {
			return Html::a(Html::encode($model->name), ['view', 'id' => $model->id]);
		},
	]) ?>

</div>
