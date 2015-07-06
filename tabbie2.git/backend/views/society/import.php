<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Team */

$this->title = Yii::t('app', 'Import {modelClass}', [
	'modelClass' => 'Societies',
]);
$this->params['breadcrumbs'][] = ["label" => "Societies", "url" => "index"];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="country-import">

	<h1><?= Html::encode($this->title) ?></h1>

	<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

	<div class="team-form">
		<div class="row">
			<div class="col-xs-12">
				<?= Html::fileInput("csvFile", "", [
					'accept' => '.csv'
				]);
				?>
			</div>
		</div>
		<br>

		<div class="form-group">
			<?= Html::submitButton(Yii::t('app', 'Import'), ['class' => 'btn btn-success']) ?>
		</div>


	</div>

	<?php ActiveForm::end(); ?>
</div>