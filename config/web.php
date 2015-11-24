<?php

use yii\helpers\ArrayHelper;

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

$config = [
    'id' => 'ping-logger-web',
    'language' => 'ru-RU',
    'sourceLanguage' => 'en-US',
    'timeZone' => 'Europe/Kiev',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'extensions' => require(__DIR__ . '/../vendor/yiisoft/extensions.php'),
    'components' => [
        'assetManager' => [
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'js' => [
                        YII_ENV_DEV ? 'jquery.js' : 'jquery.min.js'
                    ]
                ],
                'app\assets\ReactAsset' => [
                    'js' => [
                        'JSXTransformer.js',
                        YII_ENV_DEV ? 'react-with-addons.js' : 'react-with-addons.min.js'
                    ]
                ],
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                'file' => [
                    'class' => 'yii\log\FileTarget',
                    'except' => [
                        'yii\web\HttpException:404',
                        'yii\web\HttpException:403',
                    ],
                    'levels' => ['error', 'warning'],
                    'logFile' => '@runtime/logs/web.log',
                ],
            ],
        ],
        'user' => [
            'class' => 'app\components\User',
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'site/index',
                '<_a:(login|logout)>' => 'site/<action>',
                '<_c:\w+>/<id:\d+>' => '<_c>/view',
                '<_c:(object|group)>' => '<_c>/index',
            ]
        ],
        'db' => $db,
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
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
        'icon-framework' => 'fa',
    ], $params),
];

$config = ArrayHelper::merge(
    $config,
    require(__DIR__ . '/' . YII_ENV . '/web.php')
);

return $config;
