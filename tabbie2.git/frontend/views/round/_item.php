<?php

use yii\helpers\Html;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<div class="col-sm-1">
    <?= Html::encode("#" . $model->id) ?>
</div>
<div class="col-sm-8">
    <?= Html::a(Html::encode($model->motion), ['view', 'id' => $model->id, "tournament_id" => $model->tournament->id]); ?>
</div>