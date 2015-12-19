<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Round */

$this->title = Yii::t('app', 'Import {modelClass} #{number}', [
    'modelClass' => 'Round',
    'number' => $model->number,
]);
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rounds'), 'url' => ['index', "tournament_id" => $tournament->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t("app", "Round #{number}", ["number" => $model->id]), 'url' => ['view', 'id' => $model->id, "tournament_id" => $tournament->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Import');
?>
<div class="round-import">

    <h1><?= Yii::t("app", "Import") . " " . Html::encode($model->name) ?></h1>

    <?php $form = ActiveForm::begin(['options' => [
        'enctype' => 'multipart/form-data',
        //'class' => 'loading'
    ]]); ?>

    <div class="round-import-form">
        <div class="row">
            <div class="col-xs-12">
                <?=
                Html::fileInput("jsonFile", null, [
                    'accept' => '.json'
                ]);
                ?>
            </div>
        </div>
        <hr>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Import'), ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
