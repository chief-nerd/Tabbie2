<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Strikes */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
		'modelClass' => 'Strikes',
	]) . ' ' . $model->team_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Strikes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->team_id, 'url' => ['view', 'team_id' => $model->team_id, 'adjudicator_id' => $model->adjudicator_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="strikes-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
