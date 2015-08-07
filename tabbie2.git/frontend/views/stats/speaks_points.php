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
		foreach (\common\models\Team::getPos() as $pos) {
			$speaks[$result->{$pos . "_A_speaks"}] += 1;
			$speaks[$result->{$pos . "_B_speaks"}] += 1;
		}
	}
}

?>

<script type="text/javascript"
		src="https://www.google.com/jsapi?autoload={
            'modules':[{
              'name':'visualization',
              'version':'1',
              'packages':['corechart']
            }]
          }"></script>

<script type="text/javascript">
	google.setOnLoadCallback(drawChart);

	function drawChart() {
		var data = google.visualization.arrayToDataTable([
			['Points', 'Volumen'],
			<?
			foreach($speaks as $k => $v)
			{
				echo "['$k',  $v],";
			}
			?>
		]);

		var options = {
			curveType: 'function',
			legend: 'none',
			width: 800,
			height: 400,
			vAxis: {
				viewWindowMode: 'maximized',
				minValue: 0,
				gridlines: {
					count: 8,
				}
			},
			hAxis: {
				gridlines: {
					count: 5,
				}
			}
		};

		var chart = new google.visualization.LineChart(document.getElementById('speaker_chart'));

		chart.draw(data, options);
	}
</script>


<h3><?php echo Yii::t("app", "Speaker Points Distribution") ?></h3>
<div class="row">
	<div class="col-xs-12 col-sm-6">
		<div id="speaker_chart"></div>
	</div>
</div>
<div class="clear"></div>
