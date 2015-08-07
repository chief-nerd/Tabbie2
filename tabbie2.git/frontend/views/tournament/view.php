<?php


use yii\widgets\DetailView;
use kartik\helpers\Html;
use \common\models\Team;
use common\models\Panel;
use yii\jui\Tabs;

/* @var $this yii\web\View */
/* @var $model common\models\Tournament */

$this->registerJs("
// Javascript to enable link to tab
			var url = document.location.toString();
			if (url.match('#')) {
				$('.nav-tabs a[href=#'+url.split('#')[1]+']').tab('show') ;
			}

			// With HTML5 history API, we can easily prevent scrolling!
			$('.nav-tabs a').on('shown.bs.tab', function (e) {
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
		<? if ($model->status >= \common\models\Tournament::STATUS_CLOSED) {
			$items = [
				[
					'label'   => Yii::t("app", "Overview"),
					'content' => $this->render("_view_overview", compact("model"))
				],
				[
					'label' => Yii::t("app", "Motions"),
					'url'   => ['stats/motion', "tournament_id" => $model->id],
				],
				[
					'label' => Yii::t("app", "Speaks Distrubution"),
					'url'   => ['stats/speaks', "tournament_id" => $model->id],
				],
				[
					'label' => Yii::t("app", "Speaker Tab"),
					'url'   => ['stats/speaker-tab', "tournament_id" => $model->id],
				],
				[
					'label' => Yii::t("app", "Team Tab"),
					'url'   => ['stats/team-tab', "tournament_id" => $model->id],
				],
			];
			echo Tabs::widget([
				'items'    => $items,
			]);

		} else {
			echo $this->render("_view_overview", compact("model"));
		}
		?>
		</div>
	</div>
</div>
