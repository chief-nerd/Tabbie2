<?php
//frontend\assets\BallotAsset::register($this);

/* @var $this yii\web\View */
/* @var $debate Debate */
?>
<div class="tournamentlogo">
	<img src="<?= $tournament->logo ?>">
</div>
<div class="tournament">
	<?= $tournament->fullname ?>
</div>
<div class="predetails">
	<table>
		<tr>
			<th><?= Yii::t("app", "Debate ID") ?></th>
			<td><?= $debate->id ?></td>
		</tr>
		<tr>
			<th><?= Yii::t("app", "Room") ?></th>
			<td><?= $debate->venue->name ?></td>
		</tr>
		<tr>
			<th><?= Yii::t("app", "Chair") ?></th>
			<td><?= $debate->getChair()->name ?></td>
		</tr>
		<?
		$panel = "";
		foreach ($debate->getAdjudicators()->all() as $adj) {
			if ($debate->chair->id != $adj->id)
				$panel .= $adj->name . ", ";
		}
		if (strlen($panel) > 0):
		?>
		<tr>
			<th><?= Yii::t("app", "Adjudicators") ?></th>
			<td><?= substr($panel, 0, -2); ?></td>
		</tr>
		<? endif; ?>
	</table>
</div>

<div class="headline">
	<table>
		<? if ($round->infoslide): ?>
			<tr class="infoslide">
				<th><?= Yii::t("app", "InfoSlide") ?>:</th>
				<td>
					<div><?= $round->infoslide ?></div>
				</td>
			</tr>
		<? endif; ?>
		<tr class="motion">
			<th><?= Yii::t("app", "Motion") ?>:</th>
			<td>
				<div><?= $round->motion ?></div>
			</td>
		</tr>
	</table>
</div>

<div class="table">
	<table cellpadding="0" cellspacing="0">
		<tr class="opening">
			<td>
				<table>
					<tr>
						<th colspan="3" class="pos"><?= Yii::t("app", "Opening Government") ?></th>
					</tr>
					<tr>
						<td colspan="3" class="team"><?= Yii::t("app", "Team") ?>: <?= $debate->og_team->name ?></td>
					</tr>
					<tr>
						<td><?= ($debate->og_team->speakerA) ? $debate->og_team->speakerA->name : "" ?></td>
						<td class="value"></td>
						<td class="rank" rowspan="2"></td>
					</tr>
					<tr>
						<td><?= ($debate->og_team->speakerB) ? $debate->og_team->speakerB->name : "" ?></td>
						<td class="value"></td>
					</tr>
				</table>
			</td>
			<td>
				<table>
					<tr>
						<th colspan="3" class="pos"><?= Yii::t("app", "Opening Opposition") ?></th>
					</tr>
					<tr>
						<td colspan="3" class="team"><?= Yii::t("app", "Team") ?>: <?= $debate->oo_team->name ?></td>
					</tr>
					<tr>
						<td><?= ($debate->oo_team->speakerA) ? $debate->oo_team->speakerA->name : "" ?></td>
						<td class="value"></td>
						<td class="rank" rowspan="2"></td>
					</tr>
					<tr>
						<td><?= ($debate->oo_team->speakerB) ? $debate->oo_team->speakerB->name : "" ?></td>
						<td class="value"></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr class="closing">
			<td>
				<table>
					<tr>
						<th colspan="3" class="pos"><?= Yii::t("app", "Closing Government") ?></th>
					</tr>
					<tr>
						<td colspan="3" class="team"><?= Yii::t("app", "Team") ?>: <?= $debate->cg_team->name ?></td>
					</tr>
					<tr>
						<td><?= ($debate->cg_team->speakerA) ? $debate->cg_team->speakerA->name : "" ?></td>
						<td class="value"></td>
						<td class="rank" rowspan="2"></td>
					</tr>
					<tr>
						<td><?= ($debate->cg_team->speakerB) ? $debate->cg_team->speakerB->name : "" ?></td>
						<td class="value"></td>
					</tr>
				</table>
			</td>
			<td>
				<table>
					<tr>
						<th colspan="3" class="pos"><?= Yii::t("app", "Closing Opposition") ?></th>
					</tr>
					<tr>
						<td colspan="3" class="team"><?= Yii::t("app", "Team") ?>: <?= $debate->co_team->name ?></td>
					</tr>
					<tr>
						<td><?= ($debate->co_team->speakerA) ? $debate->co_team->speakerA->name : "" ?></td>
						<td class="value"></td>
						<td class="rank" rowspan="2"></td>
					</tr>
					<tr>
						<td><?= ($debate->co_team->speakerB) ? $debate->co_team->speakerB->name : "" ?></td>
						<td class="value"></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>