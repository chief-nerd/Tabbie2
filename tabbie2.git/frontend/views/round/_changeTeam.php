<?

use kartik\form\ActiveForm;
use kartik\widgets\Select2;
use yii\bootstrap\Modal;
use common\models\Team;
use common\models\Debate;

/** @var $model Team */
/** @var $debate Debate */

Modal::begin([
    'options' => ['id' => 'changeTeamForm' . $model->id, 'tabindex' => false],
    'header' => '<h4 style="margin:0; padding:0">' . Yii::t("app", "Switch Team {team} with", ["team" => $model->name]) . '</h4>',
    'toggleButton' => ['label' => $model->name, 'class' => 'btn btn-sm btn-default'],
]);
$id = 'changeTeamForm_' . $model->id;
$form = ActiveForm::begin([
    'action' => ['switch-team', "id" => $debate->round_id, "did" => $debate->id, "ta" => $model->id, "tournament_id" => $debate->tournament_id],
    'method' => 'get',
    'id' => $id,
]);

echo Select2::widget([
    'name' => 'tb',
    'data' => $teamOptions,
    'options' => ['placeholder' => Yii::t("app", 'Select a Team ...')],
    "pluginEvents" => [
        "change" => "function() { document.getElementById('" . $id . "').submit(); }",
    ]
]);
$form->end();
Modal::end();
?>