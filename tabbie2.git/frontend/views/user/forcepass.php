<?php

use common\models\User;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */


$this->title = Yii::t('app', 'Force new password for {name}', [
	'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="user-forcepass-form">

	<h1><?= Html::encode($this->title) ?></h1>

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'password')->textInput(['maxlength' => 255]) ?>


	<div class="form-group">
		<?= Html::a(Yii::t('app', 'Cancel'), ["index"], ['class' => 'btn btn-default']) ?>
		<?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>