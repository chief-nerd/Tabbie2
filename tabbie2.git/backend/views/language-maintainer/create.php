<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\LanguageMaintainer */

$this->title = Yii::t('app', 'Create Language Maintainer');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Language Maintainers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="language-maintainer-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
