<?php

use common\components\UserIdentity;

return [
	'language' => 'de-DE',
	'sourceLanguage' => 'en-UK',
	'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
	'components' => [
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
