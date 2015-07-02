<?php
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use kartik\helpers\Html;
use yii\helpers\Html as Html2;
use jakobreiter\quaggajs\YiiQuagga;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ContactForm */

$this->title = Yii::t("app", 'Checkin');
$this->params['breadcrumbs'][] = ['label' => $tournament->name, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs("document.getElementById('checkinform-number').focus();", \yii\web\View::POS_READY);
?>
<div class="site-checkin">
	<h1><?= Html::encode($this->title) ?></h1>

	<div class="row" style="margin: 20px 0;">
		<div class="col-sm-12" id="messages">
			<? foreach ($messages as $msg) {
				foreach ($msg as $key => $text)
					echo '<div class="msg text-' . $key . ' bg-' . $key . ' text-center">' . $text . '</div>';
			}
			?>
		</div>
	</div>

	<?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>
	<div class="row">

		<div class="col-xs-12 col-sm-6">
			<div class="col-xs-12">
				<?= $form->field($model, 'number') ?>
			</div>
			<div class="col-xs-12">
				<?= Html::submitButton(Html::icon("send") . "&nbsp;" . Yii::t("app", 'Submit'), [
					'class' => 'btn btn-primary btn-block',
					'name' => 'contact-button']) ?>
			</div>
		</div>
		<?= $form->field($model, "camInit")->hiddenInput(["value" => $camInit])->label('') ?>
		<div class="col-xs-12 col-sm-6">
			<!--quaggaJS-->
			<? if ($camInit):
				echo YiiQuagga::widget([
					"id" => 'codereader',
					'name' => 'CheckinForm[number]',
					'target' => '#checkinform-number',
					'messages' => '#messages',
				]);
			endif;
			?>

		</div>
	</div>
</div>
<?php ActiveForm::end(); ?>

</div>
