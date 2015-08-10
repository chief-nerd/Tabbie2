<?php
use kartik\helpers\Html;
use common\models\Round;

?>

<h3><?php echo Yii::t("app", "Motions") ?></h3>
<div class="row">
	<div class="col-xs-12">
		<ul class="list-group">
			<? foreach ($model->getRounds()->where(["displayed" => 1])->all() as $round):
				/** @var Round $round */
				?>
				<li class="list-group-item">
					<div class="row">
						<div class="col-xs-12 col-sm-12 col-md-2">
							<?
							if ($model->isTabMaster(Yii::$app->user->id) || $model->isConvenor(Yii::$app->user->id)):
								?>
								<?= Html::a($round->name, ["round/view", "id" => $round->id, "tournament_id" => $model->id]); ?>
							<? else: ?>
								<?= $round->name ?>
							<? endif; ?>
						</div>
						<div class="col-xs-12 col-sm-10 col-md-8">
							<?= Html::encode($round->motion) ?>
						</div>
						<?
						if ($model->status === \common\models\Tournament::STATUS_CLOSED):
							$posMatrix = [
								"og" => 0,
								"oo" => 0,
								"cg" => 0,
								"co" => 0,
							];
							$sum = 0;
							foreach ($round->getDebates()->all() as $debate) {
								$result = $debate->result;
								if ($result instanceof \common\models\Result) {
									foreach (\common\models\Team::getPos() as $pos) {
										$posMatrix[$pos] += $result->{$pos . "_place"};
										$sum += $result->{$pos . "_place"} / 2.3;
									}
								}
							}
							$base = 30;
							foreach ($posMatrix as $pos => $pm) {
								$posMatrix[$pos . "_percent"] = round($posMatrix[$pos] / $sum, 2);
							}
							$posMatrix["og_x"] = $posMatrix["og_y"] = $base * (1 - $posMatrix["og_percent"]);

							$posMatrix["oo_x"] = $base * (1 - $posMatrix["oo_percent"]);
							$posMatrix["oo_y"] = $base * ($posMatrix["oo_percent"]) + $base;

							$posMatrix["co_x"] = $posMatrix["co_y"] = $base * ($posMatrix["co_percent"]) + $base;

							$posMatrix["cg_x"] = $base * ($posMatrix["cg_percent"]) + $base;
							$posMatrix["cg_y"] = $base * (1 - $posMatrix["cg_percent"]);
							?>
							<div class="col-xs-12 col-sm-2 col-md-2">
								<div class="balance-frame center-block">
									<svg height="100%" width="100%">
										<polygon points="
										<?php echo $posMatrix["og_x"] . "," . $posMatrix["og_y"] ?>
										<?php echo $posMatrix["oo_x"] . "," . $posMatrix["oo_y"] ?>
										<?php echo $posMatrix["co_x"] . "," . $posMatrix["co_y"] ?>
										<?php echo $posMatrix["cg_x"] . "," . $posMatrix["cg_y"] ?>"
												 style="fill:#AAF;"/>
										<line x1="0" y1="30" x2="60" y2="30" style="stroke:#DDD;stroke-width:1"/>
										<line x1="30" y1="0" x2="30" y2="60" style="stroke:#DDD;stroke-width:1"/>

										<polygon points="15,15 15,45, 45,45 45,15"
												 style="fill:transparent;stroke:#EEE;stroke-width:1"/>
									</svg>
								</div>
							</div>
						<? endif; ?>
					</div>
				</li>
			<? endforeach; ?>
		</ul>
	</div>
</div>
<div class="clear"></div>