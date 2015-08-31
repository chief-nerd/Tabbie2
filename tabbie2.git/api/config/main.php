<?php

$frontendConfig = require(__DIR__ . '/../../frontend/config/main.php');
$baseUrl = str_replace('/api/web', '', (new \yii\web\Request)->getBaseUrl());

$params = array_merge(
	require(__DIR__ . '/../../common/config/params.php'),
	require(__DIR__ . '/../../common/config/params-local.php'),
	require(__DIR__ . '/params.php'),
	require(__DIR__ . '/params-local.php')
);

return [
	'id' => 'app-api',
	'basePath' => dirname(__DIR__),
	'controllerNamespace' => 'api\controllers',
	'bootstrap' => ['gii', 'log'],
	'components' => [
		'request' => [
			'baseUrl' => $baseUrl,
			'parsers' => [
				'application/json' => 'yii\web\JsonParser',
			]
		],
		'response' => [
			'charset' => 'UTF-8',
		],
		'user' => [
			'identityClass' => 'common\models\User',
			'enableSession' => false,
			'loginUrl' => null,
			'enableAutoLogin' => false,
		],
		'urlManager' => [
			"class" => "yii\web\urlManager",
			'baseUrl' => $baseUrl,
			'enablePrettyUrl' => true,
			'enableStrictParsing' => true,
			'showScriptName' => false,
			'rules' => [
				'' => 'site/index',
				['class' => 'yii\rest\UrlRule', 'controller' => 'motion'],
				['class' => 'yii\rest\UrlRule', 'controller' => 'tournament'],
				['class' => 'yii\rest\UrlRule', 'controller' => 'society',
					'extraPatterns' => [
						'GET search' => 'search',
					]
				],
				['class' => 'yii\rest\UrlRule', 'controller' => 'user'],
			],
		],
		'urlManagerFrontend' => array_merge($frontendConfig["components"]["urlManager"], [
			"class" => "yii\web\urlManager",
			'baseUrl' => $frontendConfig["params"]["appUrl"],
		]),
		'log' => [
			'traceLevel' => YII_DEBUG ? 3 : 0,
			'targets' => [
				[
					'class' => 'yii\log\FileTarget',
					'levels' => ['error', 'warning'],
				],
			],
		],
		'errorHandler' => [
			'errorAction' => 'site/error',
		],
	],
	'params' => $params,
];
