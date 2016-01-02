<?

use kartik\form\ActiveForm;
use kartik\widgets\Select2;
use yii\bootstrap\Modal;
use common\models\Adjudicator;

/** @var $model Debate */
/** @var $adju Debate */

Modal::begin([
    'options' => ['id' => 'changeAdjudicatorForm' . $adju->id, 'tabindex' => false],
    'header' => '<h4 style="margin:0; padding:0">' . Yii::t("app", "Replace adjudicator {adjudicator} with", [
            "adjudicator" => $adju->getName()
        ]) . '</h4>',
    //'toggleButton' => ['label' => 'Replace', 'class' => 'btn btn-sm btn-default'],
]);
$id = 'replaceAdjudicatorForm_' . $adju->id;
$form = ActiveForm::begin([
    'action' => ['adjudicator/replaceadju', "id" => $adju->id, "debateid" => $model->id, "tournament_id" => $model->tournament_id],
    'method' => 'get',
    'id' => $id,
]);

echo Select2::widget([
    'name' => 'new_adju',
    'data' => $adjuOptions,
    'options' => ['placeholder' => Yii::t("app", 'Select an Adjudicator ...')],
    "pluginEvents" => [
        "change" => "function() { document.getElementById('" . $id . "').submit(); }",
    ]
]);
$form->end();
Modal::end();
?>