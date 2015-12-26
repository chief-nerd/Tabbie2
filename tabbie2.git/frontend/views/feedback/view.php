<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\feedback */

$this->title = "Feedback";
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Feedbacks'), 'url' => ['index', "tournament_id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="feedback-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?
    $att = [
        [
            "label" => "To",
            "value" => $model->to->name . " ({$model->getType_To_String()})"
        ],
        [
            "label" => "From",
            "value" => $model->from->name . " ({$model->getType_From_String()})"
        ],
        'debate.venue.name:text:Room',
        'time',
    ];
    ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => $att,
    ]) ?>

    <?
    $att = [];
    foreach ($model->answers as $answer) {
        $line = [
            'label' => $answer->question->text,
            'value' => $answer->getFormatValue(),
        ];
        array_push($att, $line);
    }
    ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => $att,
    ]) ?>

</div>
