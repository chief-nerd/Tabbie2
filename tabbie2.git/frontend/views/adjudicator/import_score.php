<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Team */

$this->title = Yii::t('app', 'Import Score for {modelClass}', [
    'modelClass' => 'Adjudicator',
]);
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Adjudicators'), 'url' => ['index', 'tournament_id' => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
    <td class="team-import">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['options' => [
        'enctype' => 'multipart/form-data',
    ]]); ?>

    <div class="round-import-form">
        <div class="row">
            <div class="col-xs-12">
                <?=
                Html::fileInput("csvFile", null, [
                    'accept' => '.csv'
                ]);
                ?>
            </div>
        </div>
        <hr>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Import'), ['class' => 'btn btn-success']) ?>

            <?= Html::submitButton('Download', ['class' => 'btn btn-success', "name" => "submit", "value" => "Download"]) ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>