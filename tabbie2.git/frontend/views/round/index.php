<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use common\models\Tournament;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Rounds');
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="round-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?
		if ($tournament->status < Tournament::STATUS_CLOSED)
			echo Html::a(Yii::t('app', 'Create {modelClass}', [
				'modelClass' => 'Round',
			]), ['create', "tournament_id" => $tournament->id], ['class' => 'btn btn-success'])
		?>
	</p>

	<?=
	ListView::widget([
		'dataProvider' => $dataProvider,
		'itemOptions' => ['class' => 'item row'],
		'itemView'    => "_item",
	])
	?>

</div>
