<?php
/**
 * _merge.php File
 *
 * @package  Tabbie2
 * @author   jareiter
 * @version
 */

use kartik\form\ActiveForm;
use kartik\widgets\Select2;
use yii\bootstrap\Modal;

Modal::begin([
	'options'      => ['id' => 'changeSocietyForm' . $model->id, 'tabindex' => false],
	'header'       => '<h4 style="margin:0; padding:0">' . Yii::t("app", "Merge Society '{society}' into ...", ["society" => $model->fullname]) . '</h4>',
	'toggleButton' => [
		'label' => \kartik\helpers\Html::icon("compressed"),
		'tag'   => 'a',
		'style' => 'cursor: pointer'
	],
]);
$id = 'changeSocietyForm_' . $model->id;
$form = ActiveForm::begin([
	'action' => ['merge', "id" => $model->id],
	'method' => 'get',
	'id'     => $id,
]);
$societyOptions = \common\models\search\SocietySearch::getSearchArray();

echo Select2::widget([
	'name'         => 'other',
	'data'         => $societyOptions,
	'options'      => [
		'placeholder' => Yii::t("app", 'Select a Mother-Society ...')
	],
	"pluginEvents" => [
		"change" => "function() { document.getElementById('" . $id . "').submit(); }",
	]
]);
$form->end();
Modal::end();