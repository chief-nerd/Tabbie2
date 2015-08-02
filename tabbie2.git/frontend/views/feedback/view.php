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
		[
			"label" => "Given to",
			"value" => (\common\models\Adjudicator::findOne($model->to_id)) ?
				\common\models\Adjudicator::findOne($model->to_id)->name :
				"not found #" . $model->to_id,
		],
		[
			"label" => "Given to role",
			"value" => ($model->to_type == \common\models\Feedback::TO_CHAIR) ? Yii::t("app", "Chair") : Yii::t("app", "Wing"),
		],
	];

	foreach ($model->answers as $answer) {
		switch ($answer->question->type) {
			case \common\models\Question::TYPE_STAR:
				$formatValue = \common\models\Question::starLabels($answer->value);
				break;
			case \common\models\Question::TYPE_CHECKBOX:
				$fv = [];
				$labels = json_decode($answer->question->param);
				foreach (json_decode($answer->value) as $v) {
					$fv[] = $labels[$v];
				}
				$formatValue = implode(", ", $fv);
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
