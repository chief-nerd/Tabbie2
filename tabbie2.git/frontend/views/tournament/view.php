<?php


use yii\widgets\DetailView;
use kartik\helpers\Html;
use \common\models\Team;
use common\models\Panel;

/* @var $this yii\web\View */
/* @var $model common\models\Tournament */

$this->registerMetaTag(["property" => "og:title", "content" => Yii::t("app", "{tournament} on Tabbie2", ["tournament" => $model->fullname])], "og:title");
$this->registerMetaTag(["property" => "og:image", "content" => $model->getLogo(true)], "og:image");
$this->registerMetaTag(["property" => "og:description", "content" =>
	Yii::t("app", "Tournament taking place from {start} to {end} hosted by {convenor} from {host} in {country}", [
		"start" => Yii::$app->formatter->asDate($model->start_date, "short"),
		"end" => Yii::$app->formatter->asDate($model->end_date, "short"),
		"convenor" => Html::encode($model->convenorUser->name),
		"host" => Html::encode($model->hostedby->fullname),
		"country" => Html::encode($model->hostedby->country->name),
	])],
	"og:description");

$this->registerLinkTag(["rel" => "apple-touch-icon", "href" => $model->getLogo(true)], "apple-touch-icon");

$this->title = $model->fullname;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tournaments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tournament-view">

	<div class="row">
		<div class="col-xs-12 col-sm-8">
			<div class="col-xs-12 col-sm-2 block-center">
				<?= $model->getLogoImage() ?>
			</div>
			<div class="col-xs-12 col-sm-10">
				<h1><?= Html::encode($this->title) ?></h1>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-md-8 leftcolumn">

			<?php
			$info = $model->getLastDebateInfo(Yii::$app->user->id);
			if ($model->status === \common\models\Tournament::STATUS_RUNNING): ?>
				<?
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
					$button_output_buffer .= "&nbsp;" . Html::a(Html::icon("film") . "&nbsp;" . Yii::t('app', 'Display Draw'), ['display/index', "tournament_id" => $model->id], ['class' => 'btn btn-default']);

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
							<div
								class="col-xs-12 col-sm-6"><?php echo Yii::t("app", "You are '{pos}' in room '{room}'", [
									"pos" => $pos,
									"room" => $info["debate"]->venue->name,
								]) ?></div>
							<div class="col-xs-12 col-sm-6"><?php echo Yii::t("app", "Round starts at: {time}", [
									"time" => Yii::$app->formatter->asTime(strtotime($info["debate"]->round->prep_started . " +15min"), "short"),
								]) ?>
							</div>
						</div>
					</div>
				<? endif; ?>
				<hr>
			<? endif; ?>

			<h3><?php echo Yii::t("app", "Rounds") ?></h3>
			<ul class="list-group">
				<? foreach ($model->getRounds()->where(["displayed" => 1])->all() as $round): ?>
					<li class="list-group-item">
						<div class="row">
							<div class="col-xs-12 col-md-3">
								<?
								$linktext = Yii::t("app", "Motion Round #{number}:", ["number" => $round->number]);
								if (Yii::$app->user->isTabMaster($model) || Yii::$app->user->isConvenor($model)):
									?>
									<?= Html::a($linktext, ["round/view", "id" => $round->id, "tournament_id" => $model->id]); ?>
								<? else: ?>
									<?= $linktext ?>
								<? endif; ?>
							</div>
							<div class="col-xs-12 col-md-9">
								<?= Html::encode($round->motion) ?>
							</div>
						</div>
					</li>
				<? endforeach; ?>
			</ul>
		</div>
		<div class="col-xs-12 col-md-4 rightcolumn">
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

</div>
