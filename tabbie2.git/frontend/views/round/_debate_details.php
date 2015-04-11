<div class="row">
	<div class="col-xs-3">
		<ul>
			<li>OG: <?= $model->og_team->society->fullname ?></li>
			<li>OO: <?= $model->oo_team->society->fullname ?></li>
			<li>CG: <?= $model->cg_team->society->fullname ?></li>
			<li>CO: <?= $model->co_team->society->fullname ?></li>
		</ul>
	</div>
	<div class="col-xs-2">
		<h3><?= Yii::t("app", "Point Bucket") ?>
			: <?= max($model->og_team->points, $model->oo_team->points, $model->cg_team->points, $model->co_team->points) ?></h3>
	</div>
	<div class="col-xs-4 messages">
		<table>
			<?php
			foreach ($model->getMessages() as $msg) {
				echo "<tr><th>" . $msg->key . "</th><td>" . $msg->msg . "</td>";
			}
			?>
		</table>
	</div>
	<div class="col-xs-3 messages">
		<h3><?= Yii::t("app", "Energy Level") ?>: <?= $model->energy ?></h3>
	</div>
</div>