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
    'name' => 'Tabbie2 API',
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
        'user' => [
            'identityClass' => \common\models\ApiUser::className(),
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
                '<action>' => 'site/<action>',
                ['class' => 'yii\rest\UrlRule', 'controller' => 'user',
                    'extraPatterns' => [
                        'GET me' => 'me',
                        'GET gettournamentrole' => 'gettournamentrole',
                        'GET generatebarcode' => 'generatebarcode'
                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'motion'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'round',
                    'extraPatterns' => [
                        'GET filter' => 'filter',
                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'tournament'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'debate',
                    'extraPatterns' => [
                        'GET filter' => 'filter',
                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'panel'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'adjudicator',
                    'extraPatterns' => [
                        'POST move' => 'move',
                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'society',
                    'extraPatterns' => [
                        'GET search' => 'search',
                    ]
                ],
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
                    'levels' => ['error', 'warning', 'info'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ],
    'params' => $params,
];
