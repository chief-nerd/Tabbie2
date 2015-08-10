<?php

use yii\helpers\Html;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<div class="col-xs-12 col-sm-2">
	<?= Html::encode($model->name) ?>
</div>
<div class="col-xs-12 col-sm-10">
	<?= Html::a(Html::encode($model->motion), [
		($model->type > \common\models\Round::TYP_IN) ? 'outround/view' : 'round/view',
		'id'            => $model->id,
		"tournament_id" => $model->tournament->id],
		["class" => "btn btn-default"]);
	?>
</div>