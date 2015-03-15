<?php

	/**
	 * @link http://www.yiiframework.com/
	 * @copyright Copyright (c) 2008 Yii Software LLC
	 * @license http://www.yiiframework.com/license/
	 */

	namespace frontend\assets;

	use yii\web\AssetBundle;

	/**
	 * @author Qiang Xue <qiang.xue@gmail.com>
	 * @since 2.0
	 */
	class RoundviewAsset extends AssetBundle {

		public $sourcePath = '@frontend/assets';
		public $css = [
			'css/round.css',
			'css/color_pattern.css',
		];
		public $js = [
			'js/adjudicatorActions.js',
		];
		public $depends = [
			'frontend\assets\AppAsset',
			'yii\web\YiiAsset',
			'yii\bootstrap\BootstrapAsset',
		];

	}
