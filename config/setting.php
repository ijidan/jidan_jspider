<?php
ini_set('display_errors', true);
ini_set('display_startup_errors', true);
ini_set('log_errors', true);
ini_set('html_errors', true);
ini_set('error_log',BASE_DIR . '/storage/log/php_error.log');
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING);
//error_reporting(E_ALL ^ E_NOTICE);
return [
    'settings' => [
        // Slim Settings
        'determineRouteBeforeAppMiddleware' => true,
        'displayErrorDetails' => true,
        // View settings
        'view' => [
            'template_path' => APP_DIR . '/Views/',
            'cache_path' => BASE_DIR . '/storage/cache/'
        ],
        // Monolog settings
        'logger' => [
            'name' => 'app',
            'path' => BASE_DIR . '/storage/log/' . date('Y-m-d') . '.log',
        ],
    ],
];