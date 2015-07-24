<?php

/**
 * Application configuration shared by all applications and test types
 */
return [
    'components' => [
        'db' => [
            'dsn' => 'mysql:host=127.0.0.1;dbname=tabbie_tests',
        ],
        'mailer' => [
            'useFileTransport' => false,
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
		'log' => [
			'targets' => [
				[
					'class'  => 'yii\log\FileTarget',
					'levels' => ['error', 'warning', 'trace'],
				],
			],
		],
    ],
];
