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

$this->registerJs("
// Javascript to enable link to tab
			var url = document.location.toString();
			if (url.match('#')) {
				$('.nav-tabs a[href=#'+url.split('#')[1]+']').tab('show') ;
			}

			// With HTML5 history API, we can easily prevent scrolling!
			$('.nav-tabs a').on('shown', function (e) {
				if(history.pushState) {
					history.pushState(null, null, e.target.hash);
				} else {
					window.location.hash = e.target.hash; //Polyfill for old browsers
				}
			});
");

$this->title = $model->fullname;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tournaments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tournament-view">

	<div class="tabarea">
		<? if ($model->status === \common\models\Tournament::STATUS_CLOSED): ?>
			<ul class="nav nav-tabs">
				<li class="active"><a data-toggle="tab" href="#Overview">Overview</a></li>
				<li><a data-toggle="tab" href="#Motion">Motions</a></li>
				<li><a data-toggle="tab" href="#Speaks">Speaks Distrubution</a></li>
				<li><a data-toggle="tab" href="#SpeakerTab">Speaker Tab</a></li>
				<li><a data-toggle="tab" href="#TeamTab">Team Tab</a></li>
			</ul>
		<? endif; ?>
		<div class="tab-content">

			<div id="Overview" class="tab-pane fade in active">
				<?php echo $this->render("_view_overview", compact("model")); ?>
			</div>
			<? if ($model->status === \common\models\Tournament::STATUS_CLOSED): ?>
				<div id="Motion" class="tab-pane fade">
					<?php echo $this->render("_view_motion", compact("model")); ?>
				</div>
				<div id="Speaks" class="tab-pane fade">
					<?php echo $this->render("_view_speaks", compact("model")); ?>
				</div>
				<div id="SpeakerTab" class="tab-pane fade">
					<?php echo $this->render("_view_tab_speaker", compact("model")); ?>
				</div>
				<div id="TeamTab" class="tab-pane fade">
					<?php echo $this->render("_view_tab_team", compact("model")); ?>
				</div>
			<? endif; ?>
		</div>
	</div>
</div>
