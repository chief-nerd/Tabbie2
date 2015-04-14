<div class="row">
	<div class="col-xs-4">
		<ul>
			<li>OG: <?= $model->og_team->society->fullname ?>
				(<?= \common\models\User::getLanguageStatusLabel($model->og_team->language_status, true) ?>)
			</li>
			<li>OO: <?= $model->oo_team->society->fullname ?>
				(<?= \common\models\User::getLanguageStatusLabel($model->oo_team->language_status, true) ?>)
			</li>
			<li>CG: <?= $model->cg_team->society->fullname ?>
				(<?= \common\models\User::getLanguageStatusLabel($model->cg_team->language_status, true) ?>)
			</li>
			<li>CO: <?= $model->co_team->society->fullname ?>
				(<?= \common\models\User::getLanguageStatusLabel($model->co_team->language_status, true) ?>)
			</li>
		</ul>
	</div>
	<div class="col-xs-5 messages">
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