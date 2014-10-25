<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\TournamentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Tournaments');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tournament-index">

    <div class="row">
        <div class="col-sm-12">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
    </div>
    <div class="row">
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    </div>
    <div class="row">
        <?=
        ListView::widget([
            'dataProvider' => $dataProvider,
            'itemOptions' => ['class' => 'item row'],
            'itemView' => function ($model, $key, $index, $widget) {
        return $this->render('_item', compact("model", "key", "index", "widget"));
    },])
        ?>
    </div>
</div>
