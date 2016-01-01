<?php

use kartik\helpers\Html;
use \common\models\Team;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ResultSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Results');
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = Yii::t("app", "Venue View");
?>
<div class="result-index">


	<div class="row">
		<div class="col-xs-12 col-sm-8">
			<h1 style="margin-top: 0px"><?= Yii::t("app", "Results for {label}", ["label" => $round->name]) ?></h1>
		</div>
		<div class="col-xs-12 col-sm-4 text-center">
            <?=
            Html::checkbox("autoupdate", false, [
                'label' => Yii::t("app", "Auto Update <i id='pjax-status' class=''></i>"),
                "data-pjax" => 0,
            ]);
            ?>
            &nbsp;|&nbsp;
            <?=
            Html::a(Html::icon("list") . "&nbsp;" . Yii::t("app", "Switch to Tableview"), ["round",
                "id" => $round_id,
                "tournament_id" => $tournament->id,
                "view" => "table",
            ], ["class" => "btn btn-default"]);
            ?>
		</div>
	</div>

    <!-- AJAX -->
    <? \yii\widgets\Pjax::begin(["id" => "debates-pjax"]) ?>
    <?=
    \common\components\widgets\GroupListView::widget([
        "dataProvider" => $dataProvider,
        "groupBy" => "venue.group",
        "itemOptions" => ["class" => "venue col-xs-12 col-sm-3 col-md-2 col-lg-2"],
        "itemView" => "_venue",
        "id" => "debates",
    ]);
    ?>
    <? \yii\widgets\Pjax::end(); ?>
</div>
