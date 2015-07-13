<?
$loader = Yii::getAlias("@frontend/assets/images/") . "Preloader.gif";
?>
<div id="loader" data-url="<?= Yii::$app->assetManager->publish($loader)[1] ?>"></div>