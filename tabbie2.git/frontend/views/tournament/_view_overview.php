<?
use kartik\helpers\Html;
use yii\widgets\DetailView;
use common\models\Panel;
use common\models\Team;

/** @var common\models\Tournament $model */
?>
<div class="row" id="tournament_title">
	<div class="col-xs-12 col-md-9" style="margin-bottom: 20px">
		<div class="col-xs-12 col-sm-2 block-center">
			<?= $model->getLogoImage() ?>
		</div>
		<div class="col-xs-12 col-sm-10">
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
			if (Yii::$app->user->isConvenor($model) || Yii::$app->user->isTabMaster($model))
				$button_output_buffer .= "&nbsp;" . Html::a(Html::icon("film") . "&nbsp;" . Yii::t('app', 'Display Draw'), ['public/rounds', "tournament_id" => $model->id], ['class' => 'btn btn-default']);

			if ($model->status != \common\models\Tournament::STATUS_CLOSED) {
				if (Yii::$app->user->isTabMaster($model) || Yii::$app->user->isConvenor($model)) {
					$button_output_buffer .= "&nbsp;" . Html::a(Html::icon("cog") . "&nbsp;" . Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']);
				}
			}

			if (strlen($button_output_buffer) > 0):
				?>
				<div class="panel panel-success">
					<div class="panel-heading">
						<h3 class="panel-title"><?php echo Yii::t("app", "Your Actions") ?></h3>
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
								<?php echo Yii::t("app", "You are '{pos}' in room '{room}'", [
									"pos" => $pos,
									"room" => $info["debate"]->venue->name,
								]) ?></div>
							<div class="col-xs-12 col-sm-6">
								<?php echo Yii::t("app", "Round starts at: {time}", [
									"time" => Yii::$app->formatter->asTime(strtotime($info["debate"]->round->prep_started . " +15min"), "short"),
								]) ?>
							</div>
						</div>
						<div class="row" style="margin-top: 15px">
							<div class="col-xs-12">
								<?php echo Yii::t("app", "Motion") ?>:
								<?php echo $info["debate"]->round->motion ?>
							</div>
						</div>
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