<?php

$params = require __DIR__ . '/params-local.php';
$db = require __DIR__ . '/db-local.php';

$config = [
    'id' => 'ping-logger-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'modules' => [
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'mutex' => [
            'class' => 'yii\mutex\FileMutex',
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
        'class' => 'yii\gii\Module',
    ];
}

return $config;
