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
	<head>
		<title>Barcodes for <?= $tournament->name ?></title>
		<style type="text/css">
			@page {
				size: 21cm 29.7cm;
				size: A4 landscape;

				margin: 0pt !important;

				@top-center {
					content: "<?= $tournament->name ?>"
				}
			}

			body {
				margin: 0;
			}

			.badge {
				float: left;
				width: 7.4cm;
				height: 10.5cm;
				border: 1px solid #eee;
				position: relative;
			}

			.name {
				position: absolute;
				top: 4.66cm;
				left: 0px;
				right: 0px;
				text-align: center;
				font-size: 20pt;
				font-family: "Bebas Neue";
			}

			.team, .society {
				font-family: "Helvetica Neue";
				font-size: 80%;
			}

			.team {
				position: absolute;
				right: 10%;
				top: 7cm;
			}

			.society {
				position: absolute;
				right: 10%;
				top: 7.7cm;
			}

			.code {
				position: absolute;
				bottom: 15px;
				left: 0px;
				right: 0px;
				text-align: center;
			}
		</style>
	</head>
<body>
<?php
foreach ($teams as $t): ?>
	<? foreach (["A", "B"] as $s):
		if (isset($t[$s])):
			?>
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
					<? echo \jakobreiter\quaggajs\BarcodeFactory::generateIMG($t[$s]["code"], $t[$s]["code"] . " " . $t[$s]["name"], 80); ?>
				</div>
			</div>
		<? endif;
	endforeach; ?>
<? endforeach; ?>
<?php
foreach ($adjus as $a): ?>
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
			<? echo \jakobreiter\quaggajs\BarcodeFactory::generateIMG($a["code"], $a["code"] . " " . $a["name"], 80); ?>
		</div>
	</div>
<? endforeach; ?>