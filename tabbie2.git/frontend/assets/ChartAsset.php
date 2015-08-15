<?php

/**
 * @link      http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license   http://www.yiiframework.com/license/
 */

namespace frontend\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since  2.0
 */
class ChartAsset extends AssetBundle
{

	public $sourcePath = '@frontend/assets';
	public $css = [
		//'css/site.css',
	];
	public $js = [
		'js/Chart.Line.js',
		'js/tabs.js',
	];
	public $depends = [
		'frontend\assets\AppAsset',
	];
}