<?php

$cdnUrl = "http://www.jspider.com";
$appCdnUrl = "";
$toolsCdnUrl = "/tools";
return [
	'server_url'       => 'http://www.jspider.com/',
	'manager_url'      => 'http://tools.jspider.com/',
	'cdn_url_app'      => $appCdnUrl,
	'cdn_url_tools'    => $toolsCdnUrl,
	'img_url'          => 'http://image.jspider.com/',
	'img_dir'          => '/data/jspider/img',
	'languages'        => [
		'en'    => [
			'name' => 'English',
		],
		'zh_CN' => [
			'name' => '中文简体'
		]
	],
	'language_default' => 'zh_CN', //默认语言
	'cookie_salt'      => 'jspider',
	'cookie'           => [
		'domain' => '.jspider.com'
	],
	'event_log_path'   => BASE_DIR . '/storage/events/',
	'img_upload_url'   => 'http://imgupload.hinabian.com/image/save/',
	'house_save_url'   => 'https://operate.hinabian.com/estate/home/save',
];