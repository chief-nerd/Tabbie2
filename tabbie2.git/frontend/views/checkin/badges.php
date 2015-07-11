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
$height = 80;
foreach ($teams as $t): ?>
	<? foreach (["A", "B"] as $s):
		if (isset($t[$s])):
			?>
			<div class="paper" style="background-image: url(<?= $backurl ?>)">
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
	<div class="paper" style="background-image: url(<?= $backurl ?>)">
		<div class="badge">

			<div class="name">
				<? print_r($a["name"]) ?>
			</div>
			<div class="team">
				<?= "Adjudicator" ?>
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