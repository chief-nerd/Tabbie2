<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Round */

$this->title = Yii::t('app', 'Update {label}', [
	'label' => $model->name,
]);
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rounds'), 'url' => ['index', "tournament_id" => $tournament->id]];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id, "tournament_id" => $tournament->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="round-update">

	<h1><?= Yii::t("app", "Update") . " " . Html::encode($model->name) ?></h1>

	<?=
	$this->render('/round/_form', [
		'model' => $model,
	])
	?>

</div>
