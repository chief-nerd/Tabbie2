<?php


use yii\widgets\DetailView;
use kartik\helpers\Html;
use \common\models\Team;
use common\models\Panel;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $tournament common\models\Tournament */
/* @var $model frontend\models\DebregsyncForm */

$this->title = "DebReg Sync Resolve Conflicts";
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="debreg-sync-resolve">

	<?php $form = ActiveForm::begin(); ?>

	<?= Html::hiddenInput("DebregsyncForm[url]", $model->url) ?>
	<?= Html::hiddenInput("DebregsyncForm[key]", $model->key) ?>

	<? if (count($unresolved["s_fix"]) > 0): ?>`
		<h2><?php echo Yii::t("app", "Society") ?></h2>
		<? foreach ($unresolved["s_fix"] as $soc_fix): ?>
			<div class="row">
				<div class="col-xs-12 col-sm-6"><?= $soc_fix["msg"] ?></div>
				<div
					class="col-xs-12 col-sm-6"><?= Html::dropDownList("Soc[" . $soc_fix['key'] . "]", "", $soc_fix["matches"]) ?></div>
			</div>
		<? endforeach; ?>
	<? endif; ?>

	<? if (count($unresolved["a_fix"]) > 0): ?>`
		<h2><?php echo Yii::t("app", "Adjudicator") ?></h2>
		<? foreach ($unresolved["a_fix"] as $adj_fix): ?>
			<div class="row">
				<div class="col-xs-12 col-sm-6"><?= $adj_fix["msg"] ?></div>
				<div
					class="col-xs-12 col-sm-6"><?= Html::dropDownList("Adju[" . $adj_fix['key'] . "]", "", $adj_fix["matches"]) ?></div>
			</div>
		<? endforeach; ?>
	<? endif; ?>

	<? if (count($unresolved["t_fix"]) > 0): ?>`
		<h2><?php echo Yii::t("app", "Teams") ?></h2>
		<? foreach ($unresolved["t_fix"] as $team_fix): ?>
			<div class="row">
				<div class="col-xs-12 col-sm-6"><?= $team_fix["msg"] ?></div>
				<div
					class="col-xs-12 col-sm-6"><?= Html::dropDownList("Team[" . $team_fix['key'] . "]", "", $team_fix["matches"]) ?></div>
			</div>
		<? endforeach; ?>
	<? endif; ?>


	<?= Html::hiddenInput("mode", "refactor") ?>

	<div class="form-group">
		<?= Html::submitButton(\kartik\helpers\Html::icon("floppy-disk") . "&nbsp;" . Yii::t('app', 'Create'), ['class' => 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
