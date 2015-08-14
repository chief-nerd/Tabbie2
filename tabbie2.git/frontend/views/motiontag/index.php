<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\MotionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Motion Archive');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="motion-tag-index">

	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<h1><?= Html::encode($this->title) ?></h1>
		</div>
		<div class="col-xs-12 col-sm-6 text-right">
			<?= Html::a(\kartik\helpers\Html::icon("plus") . "&nbsp;" . Yii::t("app", "Add third-party Motion"), ["add-motion"], [
				"class" => "btn btn-success"
			]) ?>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="tags">
				<? foreach ($cloud as $c): ?>
					<a href="<?= \yii\helpers\Url::to(["motiontag/view", "id" => $c["id"]]) ?>"
					   class="<?= $c["size"] ?>">
						<?= $c["name"] ?>
					</a>
				<? endforeach; ?>
			</div>
		</div>
	</div>

	<?= \yii\widgets\ListView::widget([
		'dataProvider' => $dataProvider,
		'itemView'     => '_item',
	]);
	?>

</div>
