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
foreach ($person as $a): ?>
	<div class="paper" <? echo ($backurl != "") ? 'style="background-image: url(' . $backurl . ')"' : '' ?>>
		<div class="badge">

			<div class="name">
				<?= $a["name"] ?>
			</div>
			<div class="team">
				<?= $a["extra"] ?>
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