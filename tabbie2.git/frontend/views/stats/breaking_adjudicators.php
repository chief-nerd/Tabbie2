<?php
/**
 * breaking_adjudicators.php File
 * @package  Tabbie2
 * @author   jareiter
 * @version
 */
?>
<? if (count($adjudicators) > 0): ?>
	<ul class="list-group row">
		<? foreach ($adjudicators as $a): ?>
			<li class="list-group-item text-center col-xs-12 col-sm-6 col-md-3"><?= $a->name ?></li>
		<? endforeach; ?>
	</ul>
<? else: ?>
	<div class="row">
		<div class="col-xs-12 text-center"><?= Yii::t("app", "No Breaking Adjudicators defined") ?></div>
	</div>
<? endif; ?>