<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\QuestionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Questions');
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="question-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?=
        Html::a(Yii::t('app', 'Create new {modelClass}', [
                    'modelClass' => 'Question',
                ]), ['create', 'tournament_id' => $tournament->id], ['class' => 'btn btn-success'])
        ?>
    </p>

    <?=
    ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
    return Html::a(Html::encode($model->text), [
                'view',
                'id' => $model->id,
                'tournament_id' => $model->tournamentHasQuestion[0]->tournament_id,
    ]);
},
    ])
    ?>

</div>
