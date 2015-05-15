<?php

use kartik\popover\PopoverX;
use \yii\helpers\Html;
use common\models\Result;
use common\models\Team;

/* @var $model Debate */

if ($model->result instanceof Result) {
	/* @var $result Result */
	$result = $model->result;

	$popcontent = "<table width='100%'>"
		. "<tr>"
		. "<th>" . $model->og_team->name . " (" . $result->getPlaceText(Team::getPos(Team::OG)) . ")</th>"
		. "<th>" . $model->oo_team->name . " (" . $result->getPlaceText(Team::getPos(Team::OO)) . ")</th>"
		. "</tr><tr>"
		. "<td>" . (($model->og_team->speakerA) ? $model->og_team->speakerA->name : \common\models\User::NONE) . ": " . $result->getSpeakerSpeaks(Team::getPos(Team::OG), Team::POS_A) . "<br/>"
		. (($model->og_team->speakerB) ? $model->og_team->speakerB->name : \common\models\User::NONE) . ": " . $result->getSpeakerSpeaks(Team::getPos(Team::OG), Team::POS_B) . "</td>"
		. "<td>" . (($model->oo_team->speakerA) ? $model->og_team->speakerA->name : \common\models\User::NONE) . ": " . $result->getSpeakerSpeaks(Team::getPos(Team::OO), Team::POS_A) . "<br/>"
		. (($model->oo_team->speakerB) ? $model->oo_team->speakerB->name : \common\models\User::NONE) . ": " . $result->getSpeakerSpeaks(Team::getPos(Team::OO), Team::POS_B) . "</td>"
		. "</tr><tr>"
		. "<th>" . $model->cg_team->name . " (" . $result->getPlaceText(Team::getPos(Team::CG)) . ")</th>"
		. "<th>" . $model->co_team->name . " (" . $result->getPlaceText(Team::getPos(Team::CO)) . ")</th>"
		. "</tr><tr>"
		. "<td>" . (($model->cg_team->speakerA) ? $model->cg_team->speakerA->name : \common\models\User::NONE) . ": " . $result->getSpeakerSpeaks(Team::getPos(Team::CG), Team::POS_A) . "<br/>"
		. (($model->cg_team->speakerB) ? $model->cg_team->speakerB->name : \common\models\User::NONE) . ": " . $result->getSpeakerSpeaks(Team::getPos(Team::CG), Team::POS_B) . "</td>"
		. "<td>" . (($model->co_team->speakerA) ? $model->co_team->speakerA->name : \common\models\User::NONE) . ": " . $result->getSpeakerSpeaks(Team::getPos(Team::CO), Team::POS_A) . "<br/>"
		. (($model->co_team->speakerB) ? $model->co_team->speakerB->name : \common\models\User::NONE) . ": " . $result->getSpeakerSpeaks(Team::getPos(Team::CG), Team::POS_B) . "</td>"
		. "</tr>"
		. "</table>";
}
else
	$popcontent = Yii::t("app", "No results yet!");
?>
<?=

PopoverX::widget([
	'header' => Yii::t("app", "Results in Room: {venue}", ["venue" => $model->venue->name]),
	'size' => 'md',
	'placement' => PopoverX::ALIGN_TOP,
	'content' => $popcontent,
	'toggleButton' => [
		'label' => '<div class="status ' . (($model->result) ? "entered" : "missing") . '"></div>' . $model->venue->name,
		'class' => 'btn btn-default',
	],
]);
?>

