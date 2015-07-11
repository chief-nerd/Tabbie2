<?php
/**
 * barcodes.php File
 *
 * @package  Tabbie2
 * @author   jareiter
 * @version
 */
$height = 100;
?>
	<!DOCTYPE html>
	<html>
	<head>
		<title>Barcodes for <?= $tournament->name ?></title>
		<link href="<?= Yii::$app->assetManager->publish(Yii::getAlias('@frontend/assets/css/ballot.css'))[1]; ?>"
		      rel="stylesheet">
		<style type="text/css">
			@page {
				size: 21cm 29.7cm;
				size: A4 portrait;

				margin: 0pt !important;

				@top-center {
					content: "<?= $tournament->name ?>"
				}
			}

			body {
				margin: 0;
				margin-top: 25px;
			}

			.code {
				float: left;
				border: 1px solid #eee;
				width: 273px;
				height: <?= $height ?>px;
			}
		</style>
	</head>
<body>
<?php
for ($i = 0; $i < $offset; $i++)
	echo \kartik\helpers\Html::tag("div", "", ["class" => "code"]);

foreach ($codes as $c): ?>
	<div class="code">
		<?
		echo \jakobreiter\quaggajs\BarcodeFactory::generateIMG($c["id"], $c["id"] . " " . $c["label"], $height);
		?>
	</div>
<? endforeach; ?>