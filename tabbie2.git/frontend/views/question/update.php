<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\question */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
		'modelClass' => 'Question',
	]) . ' ' . $model->id;
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Questions'), 'url' => ['index', "tournament_id" => $tournament->id]];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id, "tournament_id" => $tournament->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="question-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?=
	$this->render('_form', [
		'model' => $model,
	])
	?>

</div>
