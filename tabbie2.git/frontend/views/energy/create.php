<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\EnergyConfig */

$this->title = Yii::t('app', 'Create {modelClass}', [
	'modelClass' => 'Energy Config',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Energy Configs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="energy-config-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
