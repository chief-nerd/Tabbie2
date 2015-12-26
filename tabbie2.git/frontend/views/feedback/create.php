<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\feedback */

$this->title = Yii::t('app', 'Create {modelClass}', [
	'modelClass' => 'Feedback',
]);
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="feedback-create">

	<h1><?= Html::encode($this->title) ?></h1>
	<br>

	<?= $this->render('_form', [
		'model_group' => $model_group,
	]) ?>

</div>
