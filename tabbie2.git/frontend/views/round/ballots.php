<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>
<?php
/** @var \common\models\Round $debate */
foreach ($model->getDebates()->orderBy("venue_id")->all() as $debate): ?>
	<div class="page">
		<?
		echo $this->render('_ballotTemplate', [
			'tournament' => $model->tournament,
			'round'  => $model,
			'debate' => $debate,
		]);
		?>
	</div>
<? endforeach; ?>
</body>
</html>