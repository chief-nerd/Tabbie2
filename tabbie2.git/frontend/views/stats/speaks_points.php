<?php
use kartik\helpers\Html;
use common\models\Round;

$speaks = [];

for ($i = Yii::$app->params["speaks_min"]; $i <= Yii::$app->params["speaks_max"]; $i++) {
	$speaks[$i] = 0;
}

foreach ($model->getRounds()->all() as $round) {
	foreach ($round->getDebates()->all() as $debate) {
		$result = $debate->result;
		if ($result) {
			foreach (\common\models\Team::getPos() as $pos) {
				$speaks[$result->{$pos . "_A_speaks"}] += 1;
				$speaks[$result->{$pos . "_B_speaks"}] += 1;
			}
		}
	}
}

?>

<h3><?php echo Yii::t("app", "Speaker Points Distribution") ?></h3>
<div class="row">
	<div class="col-xs-12 col-sm-6">
		<canvas id="speaker_chart" width="100%" height="500"></canvas>
	</div>
</div>
<div class="clear"></div>

<script type="text/javascript">
	function drawChart() {
		var ctx = document.getElementById("speaker_chart").getContext("2d");

		var data = {
			labels: [<?= implode(", ", array_keys($speaks)) ?>],
			datasets: [
				{
					label: "Speaker Points",
					fillColor: "rgba(220,220,220,0.2)",
					strokeColor: "rgba(220,220,220,1)",
					pointColor: "rgba(220,220,220,1)",
					pointStrokeColor: "#fff",
					pointHighlightFill: "#fff",
					pointHighlightStroke: "rgba(220,220,220,1)",
					data: [<?= implode(", ", $speaks) ?>]
				},
			]
		};

		var speakerPointsDist = new Chart(ctx).Line(data, options);
	}

	function init() {
		drawChart();
	}
</script>
