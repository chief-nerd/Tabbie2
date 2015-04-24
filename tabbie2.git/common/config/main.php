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
		'mailer' => [
			'class' => 'yii\swiftmailer\Mailer',
			'viewPath' => '@common/mail',
			'transport' => [
				'class' => 'Swift_SmtpTransport',
				'host' => 'smtp.mandrillapp.com',
				'username' => 'support@tabbie.org',
				'password' => '5tcVGLeklNeL7Bk2BkWg6w',
				'port' => '587',
			],
		],
	],
	'modules' => [
		'gridview' => [
			'class' => '\kartik\grid\Module'
		],
	]
];
