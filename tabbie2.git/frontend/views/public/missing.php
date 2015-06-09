<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\TournamentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Missing User');
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs('
		$.pjax.defaults.timeout = false;
        $("#pjax_poll").on("pjax:end", function() {
            $.pjax.reload({container:"#tournament-missing"});  //Reload GridView
        });
        setTimeout(function(){ $.pjax.reload({container:"#tournament-missing"}); }, 2000);
	'
);

if (Yii::$app->user->isTabMaster($tournament)) {
	$this->context->menuItems = [
		['label' => \kartik\helpers\Html::icon("fire") . "&nbsp;" . Yii::t("app", 'Mark missing as inactive'), 'url' => ["public/mark-missing", "tournament_id" => $tournament->id, "accessToken" => $tournament->accessToken], "linkOptions" => ["class" => "btn btn-default"]],
	];
}

?>

<? \yii\widgets\Pjax::begin(["id" => "pjax_poll"]) ?>
	<div id="tournament-missing">

		<div class="row">
			<div class="col-sm-6 text-center">
				<h3>Missing Speaker</h3>
				<table class="table">
					<? foreach ($teams as $team): ?>
						<tr>
							<?
							if (isset($team->speakerA->name)) {
								echo '<td>' . $team->speakerA->name . '</td>';
							}
							if (isset($team->speakerB->name)) {
								echo '<td>' . $team->speakerB->name . '</td>';
							}
							?>
						</tr>
					<? endforeach; ?>
				</table>
			</div>
			<div class="col-sm-6 text-center">
				<h3>Missing Adjudicators</h3>
				<table class="table">
					<? foreach ($adjudicators as $adj): ?>
						<tr>
							<td><?= $adj->name ?></td>
						</tr>
					<? endforeach; ?>
				</table>
			</div>
		</div>
	</div>
<? \yii\widgets\Pjax::end() ?>