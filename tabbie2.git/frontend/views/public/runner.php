<?php

use kartik\helpers\Html;
use kartik\grid\GridView;
use common\models\Result;
use kartik\widgets\SwitchInput;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ResultSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Results');
$tournament = $this->context->_getContext();

?>
<div class="result-index">

	<div class="row">
		<div class="col-xs-12 col-sm-9">
			<h1><?= Yii::t("app", "Runner View for Round #{number}", ["number" => $round_number]) ?></h1>
		</div>
		<div class="col-xs-12 col-sm-3" style="padding-top: 30px"><?=
			Html::checkbox("autoupdate", false, [
				'label' => Yii::t("app", "Auto Update <i id='pjax-status' class=''></i>"),
				"data-pjax" => 0,
			])
			?>
		</div>
	</div>

	<!-- AJAX -->
	<? \yii\widgets\Pjax::begin(["id" => "debates-pjax"]) ?>
	<?=
	\common\components\widgets\GroupListView::widget([
		"dataProvider" => $dataProvider,
		"groupBy"     => "venue.group",
		"itemOptions" => ["class" => "venue col-xs-12 col-sm-3 col-md-2 col-lg-2"],
		"itemView"    => "_venue",
		"id"          => "debates",
	]);
	?>
	<? \yii\widgets\Pjax::end(); ?>
</div>

