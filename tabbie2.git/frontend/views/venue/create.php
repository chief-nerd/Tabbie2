<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Venue */

$this->title = Yii::t('app', 'Create {modelClass}', [
	'modelClass' => 'Venue',
]);
$this->params['breadcrumbs'][] = ['label' => $model->tournament->fullname, 'url' => ['tournament/view', "id" => $model->tournament->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Venues'), 'url' => ['index', "tournament_id" => 1]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="venue-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?=
	$this->render('_form', [
		'model' => $model,
	])
	?>

</div>
