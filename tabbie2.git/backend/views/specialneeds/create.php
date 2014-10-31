<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\SpecialNeeds */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Special Needs',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Special Needs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="special-needs-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
