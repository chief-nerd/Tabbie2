<!DOCTYPE html>
<html>
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