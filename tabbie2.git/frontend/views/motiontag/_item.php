<?php
/**
 * _item.php File
 * @package  Tabbie2
 * @author   jareiter
 * @version
 *
 * @var \common\models\Motion $model
 */
?>
<div class="panel panel-default motion_group">
	<div class="panel-body">
		<div class="col-xs-12 motion"><h4><?= $model->motion ?></h4></div>
		<div class="col-xs-12 info"><?= $model->infoslide ?></div>
		<div class="col-xs-12 col-sm-6"><?= $model->tagsField ?></div>
		<div class="col-xs-12 col-sm-6 text-right">
			<?= \kartik\helpers\Html::a($model->tournament, $model->link, ["target" => "_blank"]) ?> /
			<?= $model->round ?> /
			<?= Yii::$app->formatter->asDate($model->date) ?></div>
	</div>
</div>
