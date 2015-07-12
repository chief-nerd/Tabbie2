<div id="loader">
	<div class="container">
		<?
		$loader = Yii::getAlias("@frontend/assets/images/") . "Preloader.gif";
		?>
		<img src="#" data-src="<?= Yii::$app->assetManager->publish($loader)[1] ?>" alt="Loader">
	</div>
</div>