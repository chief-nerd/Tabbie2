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
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = Yii::t("app", "Venue View");
?>
<div class="result-index">

	<h1><?= Yii::t("app", "Results for Round #{number}", ["number" => $round_number]) ?></h1>

	<p class="text-right">
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
	</p>
	<!-- AJAX -->
	<? \yii\widgets\Pjax::begin(["id" => "debates-pjax"]) ?>
	<?=
	\common\components\widgets\GroupListView::widget([
		"dataProvider" => $dataProvider,
		"groupBy" => "venue.group",
		"itemOptions" => ["class" => "venue col-xs-2"],
		"itemView" => "_venue",
		"id" => "debates",
	]);
	?>
	<? \yii\widgets\Pjax::end(); ?>
</div>
