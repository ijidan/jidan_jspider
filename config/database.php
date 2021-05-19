<?php

$host = '172.16.10.111';
$user = 'hnb';
$password = 'alyHnb2015';

return [
	"mysql"   => [
		'd_spider' => [
			"host"     => $host,
			"user"     => $user,
			"password" => $password,
		],
		'd_hnb' => [
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

