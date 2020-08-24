<?php

use yii\log\FileTarget;

return [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '',
        ],
        'log' => [
            'targets' => [
                'file' => [
                    'class' => FileTarget::class,
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
    ],
];
