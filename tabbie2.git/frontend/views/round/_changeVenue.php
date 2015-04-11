<?

use kartik\form\ActiveForm;
use kartik\widgets\Select2;
use yii\bootstrap\Modal;

Modal::begin([
	'options' => ['id' => 'changeVenueForm' . $model->venue_id],
	'header' => '<h4 style="margin:0; padding:0">' . Yii::t("app", "Switch venue {venue} with", ["venue" => $model->venue->name]) . '</h4>',
	'toggleButton' => ['label' => $model->venue->name, 'class' => 'btn btn-sm btn-default'],
]);

$form = ActiveForm::begin([
	'action' => ['changevenue', "id" => $model->round_id, "debateid" => $model->id, "tournament_id" => $model->tournament_id],
	'method' => 'get',
	'id' => 'changeVenueForm',
]);
$venueOptions = \common\models\search\VenueSearch::getSearchArray($model->tournament_id, true);

echo Select2::widget([
	'name' => 'new_venue',
	'data' => $venueOptions,
	'options' => ['placeholder' => Yii::t("app", 'Select a Venue ...')],
	"pluginEvents" => [
		"change" => "function() { document.getElementById('changeVenueForm').submit(); }",
	]
]);
ActiveForm::end();
Modal::end();
?>