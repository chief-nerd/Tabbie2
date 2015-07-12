<?php

$params = array_merge(
	require(__DIR__ . '/../../common/config/params.php'),
	require(__DIR__ . '/../../common/config/params-local.php'),
	require(__DIR__ . '/params.php'),
	require(__DIR__ . '/params-local.php')
);

$baseUrl = str_replace('/frontend/web', '', (new \yii\web\Request)->getBaseUrl());

return [
	'id' => 'app-frontend',
	'basePath' => dirname(__DIR__),
	'bootstrap' => ['log'],
	'controllerNamespace' => 'frontend\controllers',
	'components' => [
		'request' => [
			'baseUrl' => $baseUrl,
		],
		'assetManager' => [
			'linkAssets' => true,
			'appendTimestamp' => false,
			'bundles' => [
				'yii\bootstrap\BootstrapAsset' => [
					'sourcePath' => null,   // do not publish the bundle
					'css' => [
						'//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css',
					]
				],
				'yii\bootstrap\BootstrapPluginAsset' => [
					'sourcePath' => null,   // do not publish the bundle
					'js' => [
						'//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js',
					],
					'jsOptions' => ["async" => "async"]
				],
				'yii\web\JqueryAsset' => [
					'sourcePath' => null,   // do not publish the bundle
					'js' => [
						'//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js',
					]
				],
			],
		],
		'user' => [
			'identityClass' => 'common\models\User',
			'enableAutoLogin' => true,
		],
		'urlManager' => [
			'enablePrettyUrl' => true,
			'showScriptName' => false,
			'rules' => [
				'' => 'site/index',
				['class' => 'common\components\UserUrlRule'],
				['class' => 'common\components\TournamentUrlRule'],
			],
		],
		'log' => [
			'traceLevel' => YII_DEBUG ? 3 : 0,
			'targets' => [
				[
					'class' => 'yii\log\FileTarget',
					'levels' => ['error', 'warning'],
				],
				[
					'class' => 'yii\log\FileTarget',
					'categories' => ['frontend\*', 'common\*'],
					'levels' => ['error', 'warning', 'trace'],
					'logFile' => '@frontend/runtime/logs/debug.log',
				],
			],
		],
		'errorHandler' => [
			'errorAction' => 'site/error',
		],
	],
	'modules' => [
		'gii' => [
			'class' => 'yii\gii\Module', //adding gii module
			'allowedIPs' => ['127.0.0.1', '::1']  //allowing ip's
		],
	],
	'params' => $params,
];
