<?php

use yii\widgets\ActiveForm;
use kartik\widgets\Select2;

$form = ActiveForm::begin([
	'action' => ['view', "id" => $model->id, "tournament_id" => $model->tournament_id],
	'method' => 'get',
	'id' => 'filterForm',
]);
?>
	<h3>Filter</h3>
	<div class="row">
		<?
		$cols = 4;
		if ($model->tournament->has_esl)
			$cols = 3;
		?>
		<div class="col-md-<?= $cols ?>">
			<?
			echo $form->field($debateSearchModel, 'venue')->widget(Select2::classname(), [
				'data'         => \common\models\search\VenueSearch::getSearchArray($model->tournament_id),
				'options'      => [
					'placeholder' => Yii::t("app", 'Select a Venue ...')
				],
				'pluginOptions' => [
					'allowClear' => true
				],
				"pluginEvents" => [
					"change" => "function() { document.getElementById('filterForm').submit(); }",
				]
			]);
			?>
		</div>
		<div class="col-md-<?= $cols ?>">
			<?
			echo $form->field($debateSearchModel, 'team')->widget(Select2::classname(), [
				'data'         => \common\models\search\DebateSearch::getTeamSearchArray($model->tournament_id),
				'options'      => ['placeholder' => Yii::t("app", 'Select a Team ...')],
				'pluginOptions' => [
					'allowClear' => true
				],
				"pluginEvents" => [
					"change" => "function() { document.getElementById('filterForm').submit(); }",
				]
			]);
			?>
		</div>
		<? if ($model->tournament->has_esl): ?>
			<div class="col-md-<?= $cols ?>">
				<?
				echo $form->field($debateSearchModel, 'language_status')->widget(Select2::classname(), [
					'data' => \common\models\User::getLanguageStatusLabelArray(true),
					'options'      => ['placeholder' => Yii::t("app", 'Select a Language ...')],
					'pluginOptions' => [
						'allowClear' => true
					],
					"pluginEvents" => [
						"change" => "function() { document.getElementById('filterForm').submit(); }",
					]
				]);
				?>
			</div>
		<? endif; ?>
		<div class="col-md-<?= $cols ?>">
			<?
			echo $form->field($debateSearchModel, 'adjudicator')->widget(Select2::classname(), [
				'data'         => \common\models\search\DebateSearch::getAdjudicatorSearchArray($model->tournament_id),
				'options'      => ['placeholder' => Yii::t("app", 'Select an Adjudicator ...')],
				'pluginOptions' => [
					'allowClear' => true
				],
				"pluginEvents" => [
					"change" => "function() { document.getElementById('filterForm').submit(); }",
				]
			]);
			?>
		</div>
	</div>
<?php ActiveForm::end(); ?>