<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Team */

$this->title = $model->name;
$tournament = $model->tournament;
$this->params['breadcrumbs'][] = ['label' => $model->tournament->fullname, 'url' => ['tournament/view', "id" => $model->tournament->id]];
if (($tournament->isTabMaster(Yii::$app->user->id) ||
        $tournament->isConvenor(Yii::$app->user->id) ||
        $tournament->isCA(Yii::$app->user->id))
)
    $teamsBC = ['label' => Yii::t('app', 'Teams'), 'url' => ['index', "tournament_id" => $model->tournament->id]];
else
    $teamsBC = ['label' => Yii::t('app', 'Teams')];
$this->params['breadcrumbs'][] = $teamsBC;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="team-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <? if ($tournament->isTabMaster(Yii::$app->user->id) ||
            $tournament->isConvenor(Yii::$app->user->id) ||
            $tournament->isCA(Yii::$app->user->id)
    ): ?>
        <p>
            <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id, "tournament_id" => $model->tournament->id], [
                    'class' => 'btn btn-primary'])
            ?>
        </p>
    <? endif; ?>

    <?=
    DetailView::widget([
            'model' => $model,
            'attributes' => [
                    'speakerA.name:text:' . Yii::t("app", "Speaker A"),
                    'speakerB.name:text:' . Yii::t("app", "Speaker B"),
                    [
                            'attribute' => 'language_status',
                            'value' => \common\models\User::getLanguageStatusLabel($model->language_status)
                    ],
                    'points',
                    'speaks',
            ],
    ])
    ?>

    <?= \yii\widgets\ListView::widget([
            'dataProvider' => $dataRoundsProvider,
            'itemView' => '_debate',
            'viewParams' => ['teamId' => $model->id]
    ]);
    ?>

</div>
