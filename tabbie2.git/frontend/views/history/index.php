<?php

use frontend\assets\UserAsset;
use yii\widgets\ListView;

UserAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = Yii::t('app', '{modelClass}\'s History', [
	'modelClass' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'History');
?>
<div class="user-history">
	<h1><?= $this->title ?></h1>

	<div class="tournaments">
		<?= ListView::widget([
			'dataProvider' => $dataProvider,
			'itemView' => '_item',
		]) ?>
	</div>
</div>