<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\feedback */

$this->title = "Feedback";
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Feedbacks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="feedback-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			'debate.venue.name:text:Room',
			'time',
		],
	]) ?>

</div>
