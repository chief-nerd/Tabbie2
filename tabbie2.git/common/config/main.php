<?php

use common\components\UserIdentity;

return [
	'language' => 'en-UK',
	'sourceLanguage' => 'en-UK',
	'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
	'components' => [
		'db' => [
			'charset' => 'utf8',
			'enableSchemaCache' => (YII_ENV == "prod") ? true : false,
		],
		'cache' => [
			'class' => 'yii\caching\FileCache',
		],
		'user' => [
			'class' => UserIdentity::className(),
		],
		'i18n' => [
			'translations' => [
				'*' => [
					'class' => 'yii\i18n\PhpMessageSource',
					'basePath' => '@common/messages',
					'sourceLanguage' => 'en-UK',
				],
			],
		],
	],
	'modules' => [
		'gridview' => [
			'class' => '\kartik\grid\Module'
		],
	]
];
