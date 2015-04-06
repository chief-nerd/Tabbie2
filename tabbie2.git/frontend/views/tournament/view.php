<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Tournament */

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
		<div class="col-xs-12 col-sm-4 text-right">
			<?

			if ($model->status === \common\models\Tournament::STATUS_RUNNING) {
				$debate = Yii::$app->user->hasOpenFeedback($model);
				if ($debate instanceof common\models\Debate) {
					echo "&nbsp;" . Html::a(Yii::t('app', 'Enter Feedback'), ['feedback/create', "id" => $debate->id, "tournament_id" => $model->id], ['class' => 'btn btn-success']);
				}
				$debate = Yii::$app->user->hasChairedLastRound($model);
				if ($debate instanceof common\models\Debate && !$debate->result instanceof \common\models\Result) {
					echo "&nbsp;" . Html::a(Yii::t('app', 'Enter Result'), ['result/create', "id" => $debate->id, "tournament_id" => $model->id], ['class' => 'btn btn-success']);
				}
				if (Yii::$app->user->isConvenor($model))
					echo "&nbsp;" . Html::a(Yii::t('app', 'Display Draw'), ['display/index', "tournament_id" => $model->id], ['class' => 'btn btn-default']);
			}

			if ($model->status != \common\models\Tournament::STATUS_CLOSED) {
				if (Yii::$app->user->isTabMaster($model) || Yii::$app->user->isConvenor($model)) {
					echo "&nbsp;" . Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']);
				}
			}

			?>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-md-8 leftcolumn">
			<ul class="list-group">
				<? foreach ($model->getRounds()->where(["displayed" => 1])->all() as $round): ?>
					<li class="list-group-item">
						<div class="row">
							<div class="col-xs-12 col-md-3">
								<?
								$linktext = "Motion Round #" . $round->number . ":";
								if (Yii::$app->user->isTabMaster($model) || Yii::$app->user->isConvenor($model)):
									?>
									<?= Html::a($linktext, ["round/view", "id" => $round->id, "tournament_id" => $model->id]); ?>
								<? else: ?>
									<?= $linktext ?>
								<? endif; ?>
							</div>
							<div class="col-xs-12 col-md-9">
								<?= $round->motion ?>
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
