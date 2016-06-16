<?php

use kartik\helpers\Html;
use \common\models\Team;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ResultSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Motion Balance');
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="motion-balance">

    <h1 style="margin-top: 0px"><?= Yii::t("app", "Motion Balance") ?></h1>

    <?
    echo \yii\widgets\ListView::widget([
        'dataProvider' => $rounds,
        'itemView' => "_round",
    ]);
    ?>

</div>
