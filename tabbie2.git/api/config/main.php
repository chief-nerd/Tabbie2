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
    'bootstrap' => ['log'],
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
        'assetManager' => [
            'linkAssets' => true,
        ],
        'user' => [
            'identityClass' => 'common\models\User',
        ],
        'urlManager' => [
            "class" => "yii\web\urlManager",
            'baseUrl' => "/",
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'site/index',
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
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
