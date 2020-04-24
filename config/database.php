<?php

$host = '172.16.10.91';
$user = 'u_spider';
$password = 'Dj0w9F4jg0q6';

return [
	"mysql"   => [
		'd_spider_news' => [
			"host"     => $host,
			"user"     => $user,
			"password" => $password,
		]
	],
	"mongodb" => [
		'db1' => [
			'server'   => '127.0.0.1:27017',
			'database' => 'livebet_1',
			'options'  => ['replicaSet' => 'sgn1'],
		]
	]
];

