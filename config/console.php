<?php

use yii\caching\FileCache;
use yii\console\controllers\MigrateController;
use yii\gii\Module as YiiGiiModule;
use yii\log\FileTarget;
use yii\mutex\FileMutex;

$params = require __DIR__ . '/params-local.php';
$db = require __DIR__ . '/db-local.php';

$config = [
    'id' => 'ping-logger-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'modules' => [],
    'controllerMap' => [
        'migrate' => [
            'class' => MigrateController::class,
            'migrationPath' => null,
            'migrationNamespaces' => [
                'app\migrations',
            ],
        ],
    ],
    'components' => [
        'cache' => [
            'class' => FileCache::class,
        ],
        'log' => [
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'mutex' => [
            'class' => FileMutex::class,
        ],
        'db' => $db,
        'formatter' => [
            'dateFormat' => 'dd.MM.Y',
            'timeFormat' => 'H:mm:ss',
            'datetimeFormat' => 'dd.MM.Y H:mm',
            'decimalSeparator' => ',',
            'thousandSeparator' => ' ',
            'currencyCode' => 'UAH',
            'sizeFormatBase' => 1000,
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => YiiGiiModule::class,
    ];
}

return $config;
