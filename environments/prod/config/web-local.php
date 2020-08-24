<?php

use yii\caching\MemCache;

return [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '',
        ],
        'mailer' => [
            'useFileTransport' => false,
        ],
        'cache' => [
            'class' => MemCache::class,
            'servers' => [
                [
                    'host' => '127.0.0.1',
                    'port' => 11211,
                    'weight' => 60,
                ],
            ],
        ],
        'log' => [
            'targets' => [
                'file' => [
                    'class' => 'yii\log\FileTarget',
                    'except' => [
                        'yii\web\HttpException:404',
                        'yii\web\HttpException:403',
                        'yii\web\User::loginByCookie',
                    ],
                    'levels' => ['error', 'warning'],
                    'logFile' => '@runtime/logs/web.log',
                ],
            ],
        ],
        'assetManager' => [
            'linkAssets' => true,
        ],
    ],
];
