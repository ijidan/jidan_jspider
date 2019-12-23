<?php

$cdnUrl="http://cdn.smiledeer.com";
$appCdnUrl=$cdnUrl."/app";
$toolsCdnUrl=$cdnUrl."tools";
return [
	'server_url' => 'http://www.smiledeer.com/',
	'manager_url' => 'http://http://manager.smiledeer.com/',
	'cdn_url_app' => $appCdnUrl,
	'cdn_url_tools'=> $toolsCdnUrl,
	'img_url' => 'http://image.smiledeer.com/',
	'img_dir' => '/data/websites/smile_deer_img',
	'languages' => [
		'en' => [
			'name' => 'English',
		],
		'zh_CN' => [
			'name' => '中文简体'
		]
	],
	'language_default' => 'zh_CN', //默认语言
	'cookie_salt' => 'smiledeer',
	'cookie' => [
		'domain' => '.smiledeer.com'
	],
	'event_log_path' => BASE_DIR . '/storage/events/'
];