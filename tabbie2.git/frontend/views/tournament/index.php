<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\TournamentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Tournaments');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tournament-index">

	<div class="row">
		<div class="col-xs-12">
			<h1><?= Html::encode($this->title) ?></h1>
		</div>
	</div>
	<?php echo $this->render('_search', ['model' => $searchModel]); ?>

	<div class="tournaments">
		<? foreach ($dataProvider->getModels() as $tournament): ?>
			<?= $this->render('_item', compact("tournament")); ?>
		<? endforeach; ?>
	</div>
</div>
