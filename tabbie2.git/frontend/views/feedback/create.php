<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\feedback */

$this->title = Yii::t('app', 'Create {modelClass}', [
	'modelClass' => 'Feedback',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Feedbacks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="feedback-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
