<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Language */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
                'modelClass' => 'Language',
        ]) . ' ' . $model->language;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Languages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->language, 'url' => ['view', 'id' => $model->language]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="language-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
            'model' => $model,
    ]) ?>

</div>
