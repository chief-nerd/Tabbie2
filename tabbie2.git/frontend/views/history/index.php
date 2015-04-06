<?php

use frontend\assets\UserAsset;
use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;
use yii\widgets\LinkPager;

UserAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = Yii::t('app', '{modelClass}\'s History', [
	'modelClass' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'History');
?>
<div class="user-history">
	<h1><?= $this->title ?></h1>

	<? foreach ($teams as $team): ?>
		<div class="tournament">
			<div class="row">
				<div class="col-md-2">
					<?= $team->tournament->getLogoImage() ?>
				</div>
				<div class="col-md-10">
					<div class="row">
						<div class="col-md-12">
							<a href="<?= \yii\helpers\Url::to(["tournament/view", "id" => $team->tournament->id]) ?>">
								<h2><?= $team->tournament->fullname ?></h2>
							</a>
						</div>
					</div>
					<div class="row">
						<?
						$tab = \common\models\PublishTabTeam::find()->where([
							"tournament_id" => $team->tournament_id,
							"team_id" => $team->id,
						])->one();
						if ($tab):
							?>
							<div class="col-md-12">
								<table class="table">
									<thead>
									<tr>
										<th>Team</th>
										<th width="200">ENL Place</th>
										<? if ($team->tournament->has_esl && $tab->esl_place): ?>
											<th width="200">ESL Place</th>
										<? endif; ?>
										<th width="250">Team Speaker Points</th>
										<?
										$results = json_decode($tab->cache_results);

										foreach ($results as $round => $result): ?>
											<th>#<?= $round ?></th>
										<? endforeach; ?>
									</tr>
									</thead>
									<tbody>
									<tr>
										<td><?= $team->name ?></td>
										<td><?= $tab->enl_place ?></td>
										<? if ($team->tournament->has_esl && $tab->esl_place): ?>
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
							"tournament_id" => $team->tournament_id,
							"user_id" => $team->speakerA_id,
						])->one();

						$tab_B = \common\models\PublishTabSpeaker::find()->where([
							"tournament_id" => $team->tournament_id,
							"user_id" => $team->speakerB_id,
						])->one();

						if ($tab_A && $tab_B):
							?>
							<div class="col-md-12">
								<table class="table">
									<thead>
									<tr>
										<th>Speaker</th>
										<th width="200">ENL Place</th>
										<? if ($team->tournament->has_esl && $tab->esl_place): ?>
											<th width="200">ESL Place</th>
										<? endif; ?>
										<th width="250">Speaker Points</th>
										<?
										$results_A = json_decode($tab_A->cache_results);
										$results_B = json_decode($tab_B->cache_results);
										?>
									</tr>
									</thead>
									<tbody>
									<tr>
										<td><?= $team->speakerA->name ?></td>
										<td><?= $tab_A->enl_place ?></td>
										<? if ($team->tournament->has_esl && $tab->esl_place): ?>
											<td><?= $tab_A->esl_place ?></td>
										<? endif; ?>
										<td><?= $tab_A->speaks ?></td>
										<? foreach ($results_A as $round => $result): ?>
											<td><?= $result ?></td>
										<? endforeach; ?>
									</tr>
									<tr>
										<td><?= $team->speakerB->name ?></td>
										<td><?= $tab_B->enl_place ?></td>
										<? if ($team->tournament->has_esl && $tab->esl_place): ?>
											<td><?= $tab_B->esl_place ?></td>
										<? endif; ?>
										<td><?= $tab_B->speaks ?></td>
										<? foreach ($results_B as $round => $result): ?>
											<td width="50"><?= $result ?></td>
										<? endforeach; ?>
									</tr>
									</tbody>
								</table>
							</div>
						<? else: ?>
							<div class="col-md-12">
								<b>No published tab available at the moment</b>
							</div>
						<? endif; ?>
					</div>
				</div>
			</div>
		</div>

	<? endforeach; ?>

	<div style="text-align: center;">
		<?
		echo LinkPager::widget([
			'pagination' => $pages,
		]);
		?>
	</div>
</div>