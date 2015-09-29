<?php

return [
	'components' => [
		'db' => [
			'class' => 'yii\db\Connection',
			'dsn' => 'mysql:host=tabbiedb3.cnzwcwadwjrx.eu-central-1.rds.amazonaws.com;dbname=tabbie',
			'username' => 'tabbieRoot',
			'password' => '8ooHLD7XZx777G5AtT66',
			'enableSchemaCache' => true,
		],
		'mailer' => [
			'useFileTransport' => false,
			'transport' => [
				'class' => 'Swift_SmtpTransport',
				'host' => 'smtp.mandrillapp.com',
				'username' => 'admin@tabbie.org',
				'password' => 'v8yVwjk2BBk7UvNdkq5BaQ',
				'port' => '587',
			],
		],
	],
];