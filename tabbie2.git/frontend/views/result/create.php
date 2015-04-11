<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Result */

$this->title = Yii::t('app', 'Results for {venue}', ["venue" => $model->debate->venue->name]);
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="result-create">

	<?=
	$this->render('_form', [
		'model' => $model,
	])
	?>

</div>
