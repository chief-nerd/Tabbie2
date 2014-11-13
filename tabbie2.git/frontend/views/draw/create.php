<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\DrawAfterRound */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Draw After Round',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Draw After Rounds'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="draw-after-round-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
