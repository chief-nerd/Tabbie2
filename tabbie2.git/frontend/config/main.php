<?php

$params = array_merge(
	require(__DIR__ . '/../../common/config/params.php'),
	require(__DIR__ . '/../../common/config/params-local.php'),
	require(__DIR__ . '/params.php'),
	require(__DIR__ . '/params-local.php')
);

$baseUrl = str_replace('/frontend/web', '', (new \yii\web\Request)->getBaseUrl());

$config = [
	'id'         => 'app-frontend',
	'basePath'   => dirname(__DIR__),
	'bootstrap'  => ['log'],
	'controllerNamespace' => 'frontend\controllers',
	'components' => [
		'request'      => [
			'baseUrl' => $baseUrl,
		],
		'assetManager' => [
			'linkAssets' => true,
			//'appendTimestamp' => false,
		],
		'user'         => [
			'identityClass' => 'common\models\User',
			'enableAutoLogin' => true,
		],
		'urlManager' => [
			'class' => 'frontend\components\LanguageUrlManager',

			//STD settings
			'enablePrettyUrl' => true,
			'showScriptName' => false,
			'rules'          => [
				'' => 'site/index',
				['class' => 'common\components\UserUrlRule'],
				['class' => 'common\components\TournamentUrlRule'],
				['class' => 'common\components\MotiontagUrlRule'],
			],
		],
		'log'          => [
			'traceLevel' => 0, //YII_DEBUG ? 3 : 0,
			'targets' => [
				[
					'class'  => 'yii\log\FileTarget',
					'levels' => ['error', 'warning'],
					'logVars' => ['_GET', '_POST', '_FILES'],
				],
				[
					'class'   => 'yii\log\FileTarget',
					'categories' => ['frontend\*', 'common\*', 'algorithms\*'],
					'levels'  => ['error', 'warning', 'trace'],
					'logFile' => '@frontend/runtime/logs/debug.log',
					'logVars'    => ['_GET', '_POST', '_FILES'],
				],
			],
		],
		'errorHandler' => [
			'errorAction' => 'site/error',
		],
	],
	'modules'    => [
		'gii' => [
			'class' => 'yii\gii\Module', //adding gii module
			'allowedIPs' => ['127.0.0.1', '::1']  //allowing ip's
		],
	],
	'params'     => $params,
];

if (YII_ENV == "prod") // In production change to CDN hosted
	$config['components']['assetManager']['bundles'] = [
		'yii\bootstrap\BootstrapAsset'       => [
			'sourcePath' => null,   // do not publish the bundle
			'css' => [
				'//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css',
			]
		],
		'yii\bootstrap\BootstrapPluginAsset' => [
			'sourcePath' => null,   // do not publish the bundle
			'js'      => [
				'//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js',
			],
			'depends' => [
				'yii\web\JqueryAsset',
				'yii\bootstrap\BootstrapAsset',
			],
		],
		'yii\web\JqueryAsset'                => [
			'sourcePath' => null,   // do not publish the bundle
			'js' => [
				'//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js',
			]
		],
	];

return $config;