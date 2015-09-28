<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\LanguageMaintainer */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
		'modelClass' => 'Language Maintainer',
	]) . ' ' . $model->user_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Language Maintainers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->user_id, 'url' => ['view', 'user_id' => $model->user_id, 'language_language' => $model->language_language]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="language-maintainer-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
