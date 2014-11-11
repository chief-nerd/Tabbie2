<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Strikes */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Strikes',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Strikes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="strikes-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
