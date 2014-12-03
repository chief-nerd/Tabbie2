<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\models\Result;
use kartik\widgets\SwitchInput;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ResultSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Results');
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="result-index">

    <h1><?= Html::encode($this->title) ?> for Round <?= $round_id ?></h1>
    <p class="text-right">
        <?=
        Html::checkbox("autoupdate", false, [
            'label' => "Auto Update <i id='pjax-status' class=''></i>",
            "data-pjax" => 0,
        ]);
        ?>
        &nbsp;|&nbsp;
        <?=
        Html::a("Switch to Overview", ["round",
            "id" => $round_id,
            "tournament_id" => $tournament->id,
            "view" => "full",
                ], ["class" => "btn btn-default"]);
        ?>
    </p>
    <!-- AJAX -->
    <? \yii\widgets\Pjax::begin(["id" => "debates-pjax"]) ?>
    <?=
    \yii\widgets\ListView::widget([
        "dataProvider" => $dataProvider,
        "itemOptions" => ["class" => "venue col-xs-2"],
        "itemView" => "_venue",
        "id" => "debates",
    ]);
    ?>
    <? \yii\widgets\Pjax::end(); ?>
</div>
