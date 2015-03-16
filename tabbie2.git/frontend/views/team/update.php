<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Team */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
		'modelClass' => 'Team',
	]) . ' ' . $model->name;

$this->params['breadcrumbs'][] = ['label' => $model->tournament->fullname, 'url' => ['tournament/view', 'id' => $model->tournament->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teams'), 'url' => ['index', 'tournament_id' => $model->tournament->id]];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id, 'tournament_id' => $model->tournament->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="team-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?=
	$this->render('_form', [
		'model' => $model,
	])
	?>

</div>
