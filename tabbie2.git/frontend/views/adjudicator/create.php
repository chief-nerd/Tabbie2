<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Adjudicator */

$this->title = Yii::t('app', 'Create {modelClass}', [
	'modelClass' => 'Adjudicator',
]);
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Adjudicators'), 'url' => ['index', 'tournament_id' => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="adjudicator-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?=
	$this->render('_form', [
		'model' => $model,
	])
	?>

</div>
