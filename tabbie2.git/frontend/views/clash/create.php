<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Society */

$this->title = Yii::t('app', 'Add individual clash');
$user = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $user->name, 'url' => ['user/view', "id" => $user->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="clash-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
