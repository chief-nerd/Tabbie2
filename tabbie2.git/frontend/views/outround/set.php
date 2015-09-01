<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\Round */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="round-form">

	<?php $form = ActiveForm::begin(); ?>

	<?
	$urlVenueList = \yii\helpers\Url::to(['venue/list', 'tournament_id' => $model->tournament_id]);
	$urlAdjudicatorList = \yii\helpers\Url::to(['adjudicator/list', 'tournament_id' => $model->tournament_id]);
	$urlTeamList = \yii\helpers\Url::to(['team/list', 'tournament_id' => $model->tournament_id]);

	$newDataScript = <<< SCRIPT
function (query) {
    return {
      id: query.term,
      text: query.term,
      tag: true
    }
  }
SCRIPT;
	?>

	<div class="row">
		<div class="col-xs-12">
			<?
			echo $form->field($model, 'venues')->widget(Select2::className(), [
				'initValueText' => \yii\helpers\ArrayHelper::map($model->venues, "id", "name"),
				'options'       => [
					'placeholder' => Yii::t("app", 'Add {object} ...', ['object' => Yii::t("app", 'Venue')])
				],
				'pluginOptions' => [
					'multiple'           => true,
					'allowClear'         => false,
					'minimumInputLength' => 1,
					'ajax'               => [
						'url'      => $urlVenueList,
						'dataType' => 'json',
						'data'     => new JsExpression('function(term,page) { return {search:term}; }'),
						'results'  => new JsExpression('function(data,page) { return {results:data.results}; }'),
					],
					'createSearchChoice' => new JsExpression($newDataScript),
					'tags'               => true,
					'createTag'          => new JsExpression($newDataScript),
				],
			]); ?>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<?
			echo $form->field($model, 'adjudicators')->widget(Select2::className(), [
				'initValueText' => \yii\helpers\ArrayHelper::map($model->adjudicators, "id", "name"),
				'options'       => [
					'placeholder' => Yii::t("app", 'Add {object} ...', ["object" => Yii::t("app", "Adjudicator")])
				],
				'pluginOptions' => [
					'multiple'           => true,
					'allowClear'         => false,
					'minimumInputLength' => 1,
					'ajax'               => [
						'url'      => $urlAdjudicatorList,
						'dataType' => 'json',
						'data'     => new JsExpression('function(term,page) { return {search:term}; }'),
						'results'  => new JsExpression('function(data,page) { return {results:data.results}; }'),
					],
				],
			]); ?>
		</div>
	</div>

	<? for ($i = 0; $i < $amount_rooms; $i++): ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo Yii::t("app", "Room {number}", ["number" => ($i + 1)]) ?></h3>
			</div>
			<div class="panel-body">
				<div class="row">
					<?
					$initTeamScript = <<< SCRIPT
function (element, callback) {
    var id=\$(element).val();
    if (id !== "") {
        \$.ajax("{$urlTeamList}?id=" + id, {
        dataType: "json"
        }).done(function(data) { callback(data.results);});
    }
}
SCRIPT;
					?>
					<? foreach (\common\models\Team::getPos() as $index => $p): ?>
						<div class="col-xs-12 col-sm-6 col-md-3">
							<?
							echo $form->field($model, 'outDebate[' . $i . '][' . $p . '_team]')->label(\common\models\Team::getPosLabel($index))->widget(Select2::className(), [
								'options'       => [
									'placeholder' => Yii::t("app", 'Add {object} ...', ['object' => Yii::t("app", 'Team')]),
								],
								'pluginOptions' => [
									'multiple'           => false,
									'allowClear'         => false,
									'minimumInputLength' => 2,
									'ajax'               => [
										'url'      => $urlTeamList,
										'dataType' => 'json',
										'data'     => new JsExpression('function(term,page) { return {search:term}; }'),
										'results'  => new JsExpression('function(data,page) { return {results:data.results}; }'),
									],
									'escapeMarkup'       => new JsExpression('function (markup) { return markup; }'),
									'initSelection'      => new JsExpression($initTeamScript)
								],
							]); ?>
						</div>
					<? endforeach; ?>
				</div>
			</div>
		</div>
	<? endfor; ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('app', 'Create Round'),
			[
				'class' => 'btn btn-success btn-block',
			]) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
