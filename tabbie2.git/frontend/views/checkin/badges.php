<?php
/**
 * barcodes.php File
 *
 * @package  Tabbie2
 * @author   jareiter
 * @version
 */
?>
	<!DOCTYPE html>
	<html>
<body>
<?php
foreach ($teams as $t): ?>
	<? foreach (["A", "B"] as $s):
		if (isset($t[$s])):
			?>
			<div class="paper" <? echo ($backurl != "") ? 'style="background-image: url(' . $backurl . ')"' : '' ?>>
				<div class="badge">

					<div class="name">
						<?= $t[$s]["name"] ?>
					</div>
					<div class="team">
						<?= $t["name"] ?>
					</div>
					<div class="society">
						<?= $t["society"] ?>
					</div>
				</div>
				<div class="badge">
					<div class="code">
						<? echo \jakobreiter\quaggajs\BarcodeFactory::generateIMG($t[$s]["code"], $t[$s]["code"] . " " . $t[$s]["name"], $height); ?>
					</div>
				</div>
				<div class="clear"></div>
			</div>
		<? endif;
	endforeach; ?>
<? endforeach; ?>
<?php
foreach ($adjus as $a): ?>
	<div class="paper" <? echo ($backurl != "") ? 'style="background-image: url(' . $backurl . ')"' : '' ?>>
		<div class="badge">

			<div class="name">
				<? print_r($a["name"]) ?>
			</div>
			<div class="team">
				<?= ($a["strength"] == \common\models\Adjudicator::MAX_RATING) ? Yii::t("app", "Chief-Adjudicator") : Yii::t("app", "Adjudicator") ?>
			</div>
			<div class="society">
				<?= $a["society"] ?>
			</div>
		</div>
		<div class="badge">
			<div class="code">
				<? echo \jakobreiter\quaggajs\BarcodeFactory::generateIMG($a["code"], $a["code"] . " " . $a["name"], $height); ?>
			</div>
		</div>
		<div class="clear"></div>
	</div>
<? endforeach; ?>