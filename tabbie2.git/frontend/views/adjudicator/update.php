<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Adjudicator */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
            'modelClass' => 'Adjudicator',
        ]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => $model->tournament->fullname, 'url' => ['tournament/view', "id" => $model->tournament->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Adjudicators'), 'url' => ['index', "tournament_id" => $model->tournament->id]];
$this->params['breadcrumbs'][] = ['label' => $model->user->name, 'url' => ['view', 'id' => $model->id, "tournament_id" => $model->tournament->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="adjudicator-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    $this->render('_form', [
        'model' => $model,
    ])
    ?>

</div>
