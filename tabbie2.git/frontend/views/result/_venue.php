<?php

use kartik\popover\PopoverX;
use \yii\helpers\Html;
use common\models\Result;

/* @var $model Debate */

if ($model->result instanceof Result) {
	/* @var $result Result */
	$result = $model->result;

	$popcontent = "<table width='100%'>"
		. "<tr>"
		. "<th>" . $model->og_team->name . " (" . $result->og_place . ")</th>"
		. "<th>" . $model->oo_team->name . " (" . $result->oo_place . ")</th>"
		. "</tr><tr>"
		. "<td>" . $model->og_team->speakerA->name . ": " . $result->og_A_speaks . "<br/>"
		. $model->og_team->speakerB->name . ": " . $result->og_B_speaks . "</td>"
		. "<td>" . $model->oo_team->speakerA->name . ": " . $result->oo_A_speaks . "<br/>"
		. $model->oo_team->speakerB->name . ": " . $result->oo_B_speaks . "</td>"
		. "</tr><tr>"
		. "<th>" . $model->cg_team->name . " (" . $result->cg_place . ")</th>"
		. "<th>" . $model->co_team->name . " (" . $result->co_place . ")</th>"
		. "</tr><tr>"
		. "<td>" . $model->cg_team->speakerA->name . ": " . $result->cg_A_speaks . "<br/>"
		. $model->cg_team->speakerB->name . ": " . $result->cg_B_speaks . "</td>"
		. "<td>" . $model->co_team->speakerA->name . ": " . $result->co_A_speaks . "<br/>"
		. $model->co_team->speakerB->name . ": " . $result->co_B_speaks . "</td>"
		. "</tr>"
		. "</table>";
}
else
	$popcontent = "No results yet!";
?>
<?=

PopoverX::widget([
	'header' => "Results in Room: " . $model->venue->name,
	'size' => 'md',
	'placement' => PopoverX::ALIGN_TOP,
	'content' => $popcontent,
	'toggleButton' => [
		'label' => '<div class="status ' . (($model->result) ? "entered" : "missing") . '"></div>' . $model->venue->name,
		'class' => 'btn btn-default',
	],
]);
?>

