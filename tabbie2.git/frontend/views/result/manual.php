<?php


use kartik\form\ActiveForm;
use kartik\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Result */

$this->title = Yii::t('app', 'Enter {modelClass} Manual', [
	'modelClass' => 'Result',
]);
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="result-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<div id="enterform">
		<?php $form = ActiveForm::begin(); ?>

        <?
        /* @var $debate Debate */
        $debate = $model->debate;
        $cols = "col-xs-12 col-sm-6";
        $fieldOption = [
            "template" => "{label} {input}\n{hint}\n{error}",
        ];
        $textOption = ["size" => 2, "maxlength" => 2];
        ?>

        <?= $form->field($model, "debate_id")->textInput(["autofocus" => "autofocus"]); ?>

		<div class="row">
            <? foreach (\common\models\Team::getPos() as $index => $pos): ?>
                <div class="<?= $cols ?>">
                    <h3><?= \common\models\Team::getPosLabel($index) ?></h3>

                    <?= $form->field($model, $pos . '_A_speaks', $fieldOption)
                        ->label(Yii::t("app", "Speaker A"))
                        ->textInput($textOption) ?>

                    <?= $form->field($model, $pos . '_B_speaks', $fieldOption)
                        ->label(Yii::t("app", "Speaker B"))
                        ->textInput($textOption) ?>
                </div>
            <? endforeach; ?>
		</div>

        <hr>
        <div id="irregular_options" class="collapse">
            <h3>Irregular Options</h3>

            <div class="row">
                <div class="<?= $cols ?>">
                    <?= $form->field($model, "og_irregular")->dropDownList(\common\models\Team::getIrregularOptions()) ?>
                </div>
                <div class="<?= $cols ?>">
                    <?= $form->field($model, "oo_irregular")->dropDownList(\common\models\Team::getIrregularOptions()) ?>
                </div>
            </div>
            <div class="row">
                <div class="<?= $cols ?>">
                    <?= $form->field($model, "cg_irregular")->dropDownList(\common\models\Team::getIrregularOptions()) ?>
                </div>
                <div class="<?= $cols ?>">
                    <?= $form->field($model, "co_irregular")->dropDownList(\common\models\Team::getIrregularOptions()) ?>
                </div>
            </div>
            <hr>
        </div>
        <div class="row">
            <div class="col-xs-5">
                <?= Html::Button(Yii::t('app', 'Options') . "&nbsp;" . Html::icon("chevron-down"), [
                    'class' => 'btn btn-default btn-block',
                    'data-toggle' => "collapse",
                    'data-target' => "#irregular_options",
                    'aria-expanded' => "false",
                    'aria-controls' => "irregular_options",
                ]) ?>
            </div>
            <div class="col-xs-7">
                <?= Html::submitButton(Yii::t('app', 'Continue') . "&nbsp;" . Html::icon("chevron-right"), ['class' => 'btn btn-success btn-block']) ?>
            </div>
        </div>

		<?php ActiveForm::end(); ?>
	</div>

</div>
