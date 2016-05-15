<?php

return [
	'components' => [
		'db' => [
			'class' => 'yii\db\Connection',
			'dsn' => 'mysql:host=127.0.0.1;dbname=tabbie',
			'username' => 'tabbie',
			'password' => '',
			'enableSchemaCache' => true,
		],
		'mailer' => [
			'useFileTransport' => false,
			'transport' => [
				'class' => 'Swift_SmtpTransport',
				'host' => '',
				'username' => '',
				'password' => '',
				'port' => '587',
			],
		],
	],
];