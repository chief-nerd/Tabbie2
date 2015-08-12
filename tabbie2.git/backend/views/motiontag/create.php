<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\MotionTag */

$this->title = Yii::t('app', 'Create Motion Tag');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Motion Tags'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="motion-tag-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
