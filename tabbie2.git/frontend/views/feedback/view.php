<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\feedback */

$this->title = "Feedback";
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Feedback'), 'url' => ['index', "tournament_id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="feedback-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<?
	$att = [
		'debate.venue.name:text:Room',
		'time',
	];

	foreach ($model->answers as $answer) {
		array_push($att, [
			'label' => $answer->question->text,
			'value' => $answer->value,
		]);
	}
	?>

	<?= DetailView::widget([
		'model' => $model,
		'attributes' => $att,
	]) ?>

</div>
