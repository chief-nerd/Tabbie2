<?php


/* @var $model Debate */
?>
<div class="btn btn-default">
	<? echo '<div class="status ' . (($model->result) ? "entered" : "missing") . '"></div>' . $model->venue->name ?>
</div>
