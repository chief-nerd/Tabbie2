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
		switch ($answer->question->type) {
			case \common\models\Question::TYPE_STAR:
				$formatValue = \common\models\Question::starLabels($answer->value);
				break;
			default:
				$formatValue = $answer->value;
		}
		$line = [
			'label' => $answer->question->text,
			'value' => $formatValue,
		];
		array_push($att, $line);
	}
	?>

	<?= DetailView::widget([
		'model' => $model,
		'attributes' => $att,
	]) ?>

</div>
