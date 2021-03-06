<?php

use app\components\bootstraps\LoginLogger;
use app\models\User;
use kartik\icons\Icon;
use yii\caching\FileCache;
use yii\helpers\ArrayHelper;
use yii\i18n\PhpMessageSource;
use yii\swiftmailer\Mailer;

$params = require __DIR__ . '/params-local.php';
$db = require __DIR__ . '/db-local.php';

$config = [
    'id' => 'ping-logger-web',
    'language' => 'ru-RU',
    'sourceLanguage' => 'en-US',
    'timeZone' => 'Europe/Kiev',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        LoginLogger::class,
    ],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'assetManager' => [
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'js' => [
                        YII_ENV_DEV ? 'jquery.js' : 'jquery.min.js',
                    ],
                ],
                'app\assets\ReactAsset' => [
                    'js' => [
                        'JSXTransformer.js',
                        YII_ENV_DEV ? 'react-with-addons.js' : 'react-with-addons.min.js',
                    ],
                ],
            ],
        ],
        'cache' => [
            'class' => FileCache::class,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [],
        ],
        'user' => [
            'identityClass' => User::class,
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => Mailer::class,
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'db' => $db,
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => PhpMessageSource::class,
                    'basePath' => '@app/messages',
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'app.php',
                    ],
                ],
            ],
        ],
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
    'params' => ArrayHelper::merge([
        'icon-framework' => Icon::FA,
    ], $params),
];

$config = ArrayHelper::merge(
    $config,
    require(__DIR__ . '/web-local.php')
);

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.83.*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.83.*'],
    ];
}

return $config;
