<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Languages');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="language-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('app', 'Create Language'), ['create'], ['class' => 'btn btn-success']) ?>
		<?= Html::a(Yii::t('app', 'Language Maintainer'), ['language-maintainer/index'], ['class' => 'btn btn-default']) ?>
	</p>

	<?= \kartik\grid\GridView::widget([
		'dataProvider' => $dataProvider,
		'columns' => [
			[
				'class' => 'yii\grid\SerialColumn'
			],
			'language',
			'label',
			[
				'class' => \kartik\grid\DataColumn::className(),
				'attribute' => 'cover',
				'format' => 'raw',
				'value' => function ($model, $key, $index, $widget) {
					return Yii::$app->formatter->asPercent($model->cover, 2);
				},
			],
			'messagesCount',
			[
				'class' => \kartik\grid\DataColumn::className(),
				'attribute' => 'languageMaintainerLabel',
				'label' => Yii::t("app", "Language Maintainer"),
				'format' => 'raw',
			],
			'last_update',
			[
				'class' => 'yii\grid\ActionColumn',
				'template' => '{view}',
				'buttons' => [
					'view' => function ($url, $model) {
						return Html::a('<span class="glyphicon glyphicon-eye-open"></span> View', $url, [
							'title' => Yii::t('yii', 'View'),
							"class" => "btn btn-default"
						]);

					}
				],
				'urlCreator' => function ($action, $model, $key, $index) {
					if ($action === 'view') {
						$url = Yii::$app->urlManager->createUrl(['language/view', "id" => $model->language]); // your own url generation logic
						return $url;
					}
				}
			],

		],
	]); ?>

</div>
