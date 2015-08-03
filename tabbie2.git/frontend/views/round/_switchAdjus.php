<?

use kartik\form\ActiveForm;
use kartik\widgets\Select2;
use yii\bootstrap\Modal;

Modal::begin([
	'options'      => ['id' => 'switchAdjusForm', 'tabindex' => false],
	'header'       => '<h4 style="margin:0; padding:0">' . Yii::t("app", "Switch Adjudicators") . '</h4>',
	'toggleButton' => ['label' => \kartik\helpers\Html::icon("random") . "&nbsp;&nbsp;" . Yii::t("app", "Switch Adjudicators"), 'class' => 'btn btn-default'],
]);
$id = 'switchAdjusForm_' . $model->id;
$form = ActiveForm::begin([
	'action' => ['switch-adjudicators', "id" => $model->id, "tournament_id" => $model->tournament_id],
	'method' => 'get',
	'id'     => $id,
]);
$userOptions = \common\models\search\UserSearch::getSearchTournamentArray($model->tournament_id, true);

echo Select2::widget([
	'name'    => 'aID',
	'data'    => $userOptions,
	'options' => ['placeholder' => Yii::t("app", 'Switch this Adjudicator ...')],
]);
echo \kartik\helpers\Html::tag("div", Yii::t("app", "with"), ["class" => "text-center"]);
echo Select2::widget([
	'name'    => 'bID',
	'data'    => $userOptions,
	'options' => ['placeholder' => Yii::t("app", 'with this one ...')],
]);
echo \kartik\helpers\Html::submitButton(Yii::t("app", "Make it so!"), ["class" => "btn btn-block"]);
$form->end();
Modal::end();