<?php

return [
	"mysql"   => [
		'db1' => [
			"host"     => "127.0.0.1",
			"user"     => "root",
			"password" => "zhuan1234",
			"database" => "viet",
			"prefix"   => "self_"
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

