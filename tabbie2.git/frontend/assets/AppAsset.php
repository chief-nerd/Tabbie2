<?php

/**
 * @link      http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license   http://www.yiiframework.com/license/
 */

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since  2.0
 */
class AppAsset extends AssetBundle {

	public $sourcePath = '@frontend/assets';
	public $css = [
		'css/site.css',
		'css/tournament.css',
		'css/result.css',
		'//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css',
	];
	public $js = [
		'js/site.js',
	];
	public $depends = [
		'yii\web\YiiAsset',