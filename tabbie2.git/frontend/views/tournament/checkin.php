<?php
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ContactForm */

$this->title = 'Checkin';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-checkin">
	<h1><?= Html::encode($this->title) ?></h1>

	<div class="row" style="margin: 20px 0;">
		<div class="col-sm-12">
			<? foreach ($messages as $msg) {
				foreach ($msg as $key => $text)
					echo '<div class="msg text-' . $key . ' bg-' . $key . ' text-center">' . $text . '</div>';
			}
			?>
		</div>
	</div>

	<?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>
	<div class="row">
		<div class="col-sm-5">
			<?= $form->field($model, 'number') ?>
		</div>
		<div class="col-sm-5">
			<?= $form->field($model, 'key') ?>
		</div>
		<div class="col-sm-2">
			<?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
		</div>

	</div>
</div>
<?php ActiveForm::end(); ?>

</div>
