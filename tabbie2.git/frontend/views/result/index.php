<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ResultSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Results');
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="result-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?=
        Html::a(Yii::t('app', 'Manually Enter {modelClass}', [
                    'modelClass' => 'Result',
                ]), ['manual', "tournament_id" => $tournament->id], ['class' => 'btn btn-success'])
        ?>
    </p>

    <?
    echo ListView::widget([
        'dataProvider' => $rounds,
        'itemView' => function ($model, $key, $index, $widget) {
            return Html::a("Round " . $model->id, ['round', "id" => $model->id, "tournament_id" => $model->tournament_id], ['class' => 'btn btn-default']);
        },
            ]);
            ?>
</div>
