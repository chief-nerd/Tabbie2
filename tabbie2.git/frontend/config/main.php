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
		'view' => [
			'class' => '\rmrevin\yii\minify\View',
			'compress_output' => !YII_DEBUG,
			'enableMinify' => !YII_DEBUG,
			'base_path' => '@app/web', // path alias to web base
			'minify_path' => '@app/web/minify', // path alias to save minify result
			'js_position' => [\yii\web\View::POS_END], // positions of js files to be minified
			'force_charset' => 'UTF-8', // charset forcibly assign, otherwise will use all of the files found charset
			'expand_imports' => true, // whether to change @import on content
		]
	],
	'modules' => [
		'gii' => [
			'class' => 'yii\gii\Module', //adding gii module
			'allowedIPs' => ['127.0.0.1', '::1']  //allowing ip's
		],
	],
	'params' => $params,
];
