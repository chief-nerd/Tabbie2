<?php
//frontend\assets\BallotAsset::register($this);

/* @var $this yii\web\View */
/* @var $debate Debate */
?>
<div id="logo">
	<img src="<?= $tournament->logo ?>">
</div>
<div id="tournament">
	<?= $tournament->fullname ?>
</div>
<div id="predetails">
	<table>
		<tr>
			<th>Debate ID</th>
			<td><?= $debate->id ?></td>
		</tr>
		<tr>
			<th>Room</th>
			<td><?= $debate->venue->name ?></td>
		</tr>
		<tr>
			<th>Chair</th>
			<td><?= $debate->getChair()->name ?></td>
		</tr>
		<?
		$panel = "";
		foreach ($debate->getAdjudicators()->all() as $adj) {
			if ($debate->chair->id != $adj->id)
				$panel .= $adj->name . ", ";
		}
		?>
		<tr>
			<th>Adjudicators</th>
			<td><?= substr($panel, 0, -2); ?></td>
		</tr>
	</table>
</div>

<div id="headline">
	<table>
		<? if ($round->infoslide): ?>
			<tr>
				<th>InfoSlide:</th>
				<td><?= $round->infoslide ?></td>
			</tr>
		<? endif; ?>
		<tr>
			<th>Motion:</th>
			<td><?= $round->motion ?></td>
		</tr>
	</table>
</div>

<div id="table">
	<table width="100%" border="0" style="font-size: 18px">
		<tr>
			<td>
				<table width="100%">
					<tr>
						<th colspan="3">Opening Government</th>
					</tr>
					<tr>
						<td colspan="3">Team: <?= $debate->og_team->name ?></td>
					</tr>
					<tr>
						<td><?= $debate->og_team->speakerA->name ?></td>
					</tr>
					<tr>
						<td><?= $debate->og_team->speakerB->name ?></td>
					</tr>
				</table>
			</td>
			<td>
				<table width="100%">
					<tr>
						<th colspan="2">Opening Opposition</th>
					</tr>
					<tr>
						<td colspan="2">Team: <?= $debate->oo_team->name ?></td>
					</tr>
					<tr>
						<td><?= $debate->oo_team->speakerA->name ?></td>
					</tr>
					<tr>
						<td><?= $debate->oo_team->speakerB->name ?></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<table width="100%">
					<tr>
						<th colspan="2">Closing Government</th>
					</tr>
					<tr>
						<td colspan="2">Team: <?= $debate->cg_team->name ?></td>
					</tr>
					<tr>
						<td><?= $debate->cg_team->speakerA->name ?></td>
					</tr>
					<tr>
						<td><?= $debate->cg_team->speakerB->name ?></td>
					</tr>
				</table>
			</td>
			<td>
				<table width="100%">
					<tr>
						<th colspan="2">Closing Opposition</th>
					</tr>
					<tr>
						<td colspan="2">Team: <?= $debate->co_team->name ?></td>
					</tr>
					<tr>
						<td><?= $debate->co_team->speakerA->name ?></td>
					</tr>
					<tr>
						<td><?= $debate->co_team->speakerB->name ?></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>