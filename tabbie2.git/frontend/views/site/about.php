<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile("https://www.google.com/jsapi", ["position" => \yii\web\View::POS_HEAD]);

$googleMaps = "
google.load('visualization', '1', {'packages': ['geochart']});
google.setOnLoadCallback(drawMarkersMap);

function drawMarkersMap() {
      var data = google.visualization.arrayToDataTable([
        ['City', 'Society name', 'Registered debaters'],
        ";

foreach ($societies as $s) {
	if ($s["city"] == "") continue;

	$googleMaps .= "['" . addslashes($s["city"]) . ", " . addslashes($s["country"]) . "', '" . addslashes($s["fullname"]) . "',  " . $s["amount"] . "], \n";
}

$googleMaps .= "
      ]);

      var options = {
        sizeAxis: { minValue: 0, maxValue: 100 },
        //region: '150', // Europe
        displayMode: 'markers',
        colorAxis: {colors: ['#e7711c', '#4374e0']} // orange to blue
      };

	  var chart_div = document.getElementById('chart_div');
      var chart = new google.visualization.GeoChart(chart_div);


      chart.draw(data, options);
    };
";

$this->registerJs($googleMaps, \yii\web\View::POS_END);
?>
<div class="site-about">
	<h1><?= Html::encode($this->title) ?></h1>

	<p>Tabbie 2 is the new edition of the famous Tabbie system. It comes freshly pimped and rewritten from the core to
		handle small and large tournaments at the same time.</p>

	<div class="row">
		<div class="col-xs-12">
			<h2>Debating Societies around the World</h2>

			<div id="chart_div" style="width: 100%; height: 500px;"></div>
			<div class="clear"></div>
		</div>
	</div>
</div>
