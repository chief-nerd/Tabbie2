<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>
<?php
foreach ($model->debates as $debate): ?>
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