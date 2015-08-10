<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Round */

$this->title = Yii::t('app', 'Create {modelClass}', [
	'modelClass' => 'Out-Round',
]);
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rounds'), 'url' => ['index', "tournament_id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="round-create">

	<h1><?= Yii::t("app", "Create") . " " . Html::encode($model->name) ?></h1>

	<?=
	$this->render('_form', [
		'model'        => $model,
		'amount_rooms' => $amount_rooms,
	])
	?>

</div>
