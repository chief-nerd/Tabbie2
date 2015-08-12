<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\MotionTag */

$this->title = $model->name . (($model->abr != null) ? " (" . $model->abr . ")" : "");
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Motion Tags'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="motion-tag-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= \yii\widgets\ListView::widget([
		'dataProvider' => $dataProvider,
		'itemView'     => '_item',

	]);
	?>

</div>
