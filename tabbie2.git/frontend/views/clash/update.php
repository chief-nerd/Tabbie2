<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Society */

$this->title = Yii::t('app', 'Update individual clash');
$user = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $user->name, 'url' => ['user/view', "id" => $user->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="clash-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
