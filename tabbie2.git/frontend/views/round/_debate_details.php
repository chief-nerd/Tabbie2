<div class="row">
	<div class="col-xs-4">
		<ul>
			<li>OG: <?= $model->og_team->society->fullname ?></li>
			<li>OO: <?= $model->oo_team->society->fullname ?></li>
			<li>CG: <?= $model->cg_team->society->fullname ?></li>
			<li>CO: <?= $model->co_team->society->fullname ?></li>
		</ul>
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
	<div class="col-xs-4 messages">
		<h3>Energy Level: <?= $model->energy ?></h3>
	</div>
</div>