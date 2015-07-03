<!DOCTYPE html>
<html>
<head>
	<title>Ballot for Round #<?= $model->number ?></title>
	<link href="<?= Yii::$app->assetManager->publish(Yii::getAlias('@frontend/assets/css/ballot.css'))[1]; ?>"
	      rel="stylesheet">
	<style type="text/css">
		@page {
			size: 29.7cm 21cm;
			size: A4 landscape;

			margin: 30pt;

			@top-center {
				content: "<?= $model->tournament->name ?>"
			}
		}
	</style>
</head>
<body>
<?php
foreach ($model->debates as $debate): ?>
	<div class="page">
		<?
		echo $this->render('_ballotTemplate', [
			'tournament' => $model->tournament,
			'round' => $model,
			'debate' => $debate,
		]);
		?>
	</div>
<? endforeach; ?>
</body>
</html>