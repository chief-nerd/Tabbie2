<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Round */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Round',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rounds'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="round-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
