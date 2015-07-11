<?
use kartik\helpers\Html;
use yii\widgets\DetailView;
use common\models\Panel;
use common\models\Team;

/** @var common\models\Tournament $model */
?>
<div class="row" id="tournament_title">
	<div class="col-xs-12 col-md-9" style="margin-bottom: 20px">
		<div class="col-xs-6 col-sm-2 block-center">
			<?= $model->getLogoImage("auto", "100") ?>
		</div>
		<div class="col-xs-6 col-sm-10 tournament_title">
			<h1><?= Html::encode($this->title) ?></h1>
		</div>
	</div>
	<?php
	if ($model->status === \common\models\Tournament::STATUS_RUNNING): ?>
		<div class="col-xs-12 col-md-9">
			<?
			$info = $model->getLastDebateInfo(Yii::$app->user->id);
			$button_output_buffer = "";
			if ($info) {
				if (Yii::$app->user->hasChairedLastRound($info) && !$info['debate']->result instanceof \common\models\Result) {
					$button_output_buffer .= "&nbsp;" . Html::a(Html::icon("envelope") . "&nbsp;" . Yii::t('app', 'Enter Result'), ['result/create', "id" => $info['debate']->id, "tournament_id" => $model->id], ['class' => 'btn btn-success']);
				}
				$ref = Yii::$app->user->hasOpenFeedback($info);
				if (is_array($ref) && $model->getTournamentHasQuestions()->count() > 0) {
					$button_output_buffer .= "&nbsp;" . Html::a(Html::icon("comment") . "&nbsp;" . Yii::t('app', 'Enter Feedback'), array_merge($ref, ['feedback/create', "tournament_id" => $model->id]), ['class' => 'btn btn-success']);
				}
			}
			else {
				$role = $model->user_role();
				if ($role != false) {
					if ($role instanceof Team) {
						$button_output_buffer .= Yii::t("app", "You are registered as team <br> '{team}' together with {teammate} for {society}", [
							"team" => $role->name,
							"teammate" => ($role->speakerA_id == Yii::$app->user->id) ? $role->speakerB->name : $role->speakerA->name,
							"society" => $role->society->fullname,
						]);
					}
					elseif ($role instanceof \common\models\Adjudicator) {
						$button_output_buffer .= Yii::t("app", "You are registered as adjudicator for {society}", [
							"society" => $role->society->fullname,
						]);
					}
				}
			}
			if ($model->isConvenor(Yii::$app->user->id) || $model->isTabMaster(Yii::$app->user->id))
				$button_output_buffer .= "&nbsp;" . Html::a(Html::icon("film") . "&nbsp;" . Yii::t('app', 'Display Draw'), ['public/rounds', "tournament_id" => $model->id, "accessToken" => $model->accessToken], ['class' => 'btn btn-default']);

			if (strlen($button_output_buffer) > 0):
				?>
				<div class="panel panel-success">
					<div class="panel-heading">
						<h3 class="panel-title"><?php echo Yii::t("app", "Personal Panel") ?></h3>
					</div>
					<div class="panel-body">
						<?php echo $button_output_buffer ?>
					</div>
				</div>
			<? endif; ?>

			<? if ($info): ?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><?php echo Yii::t("app", "Round #{num} Info", ["num" => $info["debate"]->round->number]) ?></h3>
					</div>
					<div class="panel-body">
						<?php
						if ($info["type"] == "team") {
							$pos = Team::getPosLabel($info["pos"]);
						}

						if ($info["type"] == "judge") {
							$pos = Panel::getFunctionLabel($info["pos"]);
						}
						?>
						<div class="row">
							<div class="col-xs-12 col-sm-6">
								<?php echo Yii::t("app", "You are {pos} in room {room}.", [
									"pos" => strtolower($pos),
									"room" => $info["debate"]->venue->name,
								]) ?></div>
							<div class="col-xs-12 col-sm-6">
								<?php echo Yii::t("app", "Round starts at: {time}", [
									"time" => Yii::$app->formatter->asTime(strtotime($info["debate"]->round->prep_started . " +15min"), "short"),
								]) ?>
							</div>
						</div>
						<div class="row" style="margin-top: 10px">
							<div class="col-xs-12">
								<?php echo Yii::t("app", "Motion") ?>:
								<?php echo $info["debate"]->round->motion ?>
							</div>
							<? if ($info["debate"]->round->infoslide): ?>
								<div class="col-xs-12">
									<?php echo Yii::t("app", "InfoSlide") ?>:
									<?php echo $info["debate"]->round->motion ?>
								</div>
							<? endif; ?>
						</div>
						<? if ($info["pos"] == Panel::FUNCTION_CHAIR): ?>
							<div class="row" style="margin-top: 10px">
								<div class="col-xs-12 col-sm-6">
									<?= Yii::t("app", "OG") ?>: <?= $info["debate"]->og_team->name ?><br/>
									<?= $info["debate"]->og_team->speakerA->givenname ?>
									& <?= $info["debate"]->og_team->speakerB->givenname ?>
								</div>
								<div class="col-xs-12 col-sm-6">
									<?= Yii::t("app", "OO") ?>: <?= $info["debate"]->oo_team->name ?><br/>
									<?= $info["debate"]->oo_team->speakerA->givenname ?>
									& <?= $info["debate"]->oo_team->speakerB->givenname ?>
								</div>
							</div>
							<div class="row" style="margin-top: 10px">
								<div class="col-xs-12 col-sm-6">
									<?= Yii::t("app", "CG") ?>: <?= $info["debate"]->cg_team->name ?><br/>
									<?= $info["debate"]->cg_team->speakerA->givenname ?>
									& <?= $info["debate"]->cg_team->speakerB->givenname ?>
								</div>
								<div class="col-xs-12 col-sm-6">
									<?= Yii::t("app", "CO") ?>: <?= $info["debate"]->co_team->name ?><br/>
									<?= $info["debate"]->co_team->speakerA->givenname ?>
									& <?= $info["debate"]->co_team->speakerB->givenname ?>
								</div>
							</div>
						<? endif; ?>
					</div>
				</div>
			<? endif; ?>
		</div>
	<? endif; ?>
	<div class="col-xs-12 col-md-3">
		<?=
		DetailView::widget([
			'model' => $model,
			'attributes' => [
				'id',
				'hostedby.fullname:text:Hosted By',
				'convenorUser.name:text:Convenor',
				[
					"attribute" => 'CATeam',
					'label' => "CA Team",
					'format' => 'raw',
					'value' => $model->getCATeamText(),
				],
				'tabmasterUser.name:text:Tabmaster',
				[
					"attribute" => 'start_date',
					'format' => 'raw',
					'value' => Yii::$app->formatter->asDateTime($model->start_date, "short"),
				],
				[
					"attribute" => 'end_date',
					'format' => 'raw',
					'value' => Yii::$app->formatter->asDateTime($model->end_date, "short"),
				],
			],
		])
		?>
	</div>
</div>