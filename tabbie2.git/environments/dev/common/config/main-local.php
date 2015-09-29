<?php

return [
	'components' => [
		'db' => [
			'class' => 'yii\db\Connection',
			'dsn' => 'mysql:host=127.0.0.1;dbname=tabbie',
			'username' => 'local',
			'password' => 'local',
		],
		'mailer' => [
			// send all mails to a file by default. You have to set
			// 'useFileTransport' to false and configure a transport
			// for the mailer to send real emails.
			'useFileTransport' => true,
			/*
			'transport' => [
	            'class' => 'Swift_SmtpTransport',
	            'host' => 'localhost',
	            'username' => 'username',
	            'password' => 'password',
	            'port' => '587',
	            'encryption' => 'tls',
             ],
			 */
		],
	],
];
