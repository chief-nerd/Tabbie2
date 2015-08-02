<div class="tournament">
	<div class="row">
		<div class="col-xs-12 col-sm-4 col-md-3 col-lg-2">
			<?= $model->tournament->getLogoImage(180, 180) ?>
		</div>
		<div class="col-xs-12 col-sm-8 col-md-9 col-lg-10">
			<div class="row">
				<div class="col-md-12">
					<a href="<?= \yii\helpers\Url::to(["tournament/view", "id" => $model->tournament->id]) ?>">
						<h2><?= $model->tournament->fullname ?></h2>
					</a>
				</div>
			</div>
			<div class="row">
				<?
				$tab = \common\models\PublishTabTeam::find()->where([
					"tournament_id" => $model->tournament_id,
					"team_id" => $model->id,
				])->one();
				if ($tab):
					?>
					<div class="col-md-12">
						<table class="table">
							<thead>
							<tr>
								<th><?= Yii::t("app", "Team") ?></th>
								<th width="200"><?= Yii::t("app", "EPL Place") ?></th>
								<? if ($model->tournament->has_esl && $tab->esl_place): ?>
									<th width="200"><?= Yii::t("app", "ESL Place") ?></th>
								<? endif; ?>
								<th width="250"><?= Yii::t("app", "Team Speaker Points") ?></th>
								<?
								$results = json_decode($tab->cache_results);

								foreach ($results as $round => $result): ?>
									<th>#<?= $round ?></th>
								<? endforeach; ?>
							</tr>
							</thead>
							<tbody>
							<tr>
								<td><?= $model->name ?></td>
								<td><?= $tab->enl_place ?></td>
								<? if ($model->tournament->has_esl && $tab->esl_place): ?>
									<td><?= $tab->esl_place ?></td>
								<? endif; ?>
								<td><?= $tab->speaks ?></td>
								<? foreach ($results as $round => $result): ?>
									<td width="50"><?= $result ?></td>
								<? endforeach; ?>
							</tr>
							</tbody>
						</table>
					</div>
				<? endif; ?>

				<?
				$tab_A = \common\models\PublishTabSpeaker::find()->where([
					"tournament_id" => $model->tournament_id,
					"user_id" => $model->speakerA_id,
				])->one();

				$tab_B = \common\models\PublishTabSpeaker::find()->where([
					"tournament_id" => $model->tournament_id,
					"user_id" => $model->speakerB_id,
				])->one();

				if ($tab_A || $tab_B):
					?>
					<div class="col-md-12">
						<table class="table">
							<thead>
							<tr>
								<th><? Yii::t("app", "Speaker") ?></th>
								<th width="200"><? Yii::t("app", "ENL Place") ?></th>
								<? if ($model->tournament->has_esl && $tab->esl_place): ?>
									<th width="200"><? Yii::t("app", "ESL Place") ?></th>
								<? endif; ?>
								<th width="250"><? Yii::t("app", "Speaker Points") ?></th>
							</tr>
							</thead>
							<tbody>
							<? if ($tab_A):
								$results_A = json_decode($tab_A->cache_results);
								?>
								<tr>
									<td><?= $model->speakerA->name ?></td>
									<td><?= $tab_A->enl_place ?></td>
									<? if ($model->tournament->has_esl && $tab->esl_place): ?>
										<td><?= $tab_A->esl_place ?></td>
									<? endif; ?>
									<td><?= $tab_A->speaks ?></td>
									<? foreach ($results_A as $round => $result): ?>
										<td><?= $result ?></td>
									<? endforeach; ?>
								</tr>
							<? endif;
							if ($tab_B):
								$results_B = json_decode($tab_B->cache_results);
								?>
								<tr>
									<td><?= $model->speakerB->name ?></td>
									<td><?= $tab_B->enl_place ?></td>
									<? if ($model->tournament->has_esl && $tab->esl_place): ?>
										<td><?= $tab_B->esl_place ?></td>
									<? endif; ?>
									<td><?= $tab_B->speaks ?></td>
									<? foreach ($results_B as $round => $result): ?>
										<td width="50"><?= $result ?></td>
									<? endforeach; ?>
								</tr>
							<? endif; ?>
							</tbody>
						</table>
					</div>
				<? else: ?>
					<div class="col-md-12">
						<b><?= Yii::t("app", "No published tab available at the moment") ?></b>
					</div>
				<? endif; ?>
			</div>
		</div>
	</div>
</div>