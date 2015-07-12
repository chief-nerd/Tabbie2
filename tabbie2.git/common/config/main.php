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
			'schemaCache' => 'cache',
			'schemaCacheDuration' => '3600',
			'emulatePrepare' => true,
		],
		'cache' => [
			'class' => 'yii\caching\FileCache',
		],
		'user' => [
			'class' => UserIdentity::className(),
		],
		'i18n' => [
			'translations' => [
				'yii' => [
					'class' => 'yii\i18n\PhpMessageSource',
					'basePath' => "@vendor/yiisoft/yii2/messages",
					'sourceLanguage' => 'en',
				],
				'*' => [
					'class' => 'yii\i18n\PhpMessageSource',
					'basePath' => '@common/messages',
					'sourceLanguage' => 'en_UK',
				],
			],
		],
		'mailer' => [
			'class' => 'yii\swiftmailer\Mailer',
			'viewPath' => '@common/mail',
		],
		's3' => [
			'dev' => (YII_ENV != "prod") ? true : false,
			'class' => 'common\components\AmazonS3',
			'key' => 'AKIAI3LZY3B2LAUFIX3Q',
			'secret' => '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',
			'bucket' => 'tabbie',
			'region' => 'eu-central-1'
		],
	],
	'modules' => [
		'gridview' => [
			'class' => '\kartik\grid\Module'
		],
	]
];
