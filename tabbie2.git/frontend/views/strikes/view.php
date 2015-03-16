<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Strikes */

$this->title = $model->team_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Strikes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="strikes-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('app', 'Update'), ['update', 'team_id' => $model->team_id, 'adjudicator_id' => $model->adjudicator_id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('app', 'Delete'), ['delete', 'team_id' => $model->team_id, 'adjudicator_id' => $model->adjudicator_id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
				'method' => 'post',
			],
		]) ?>
	</p>

	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			'team_id',
			'adjudicator_id',
			'approved',
		],
	]) ?>

</div>
