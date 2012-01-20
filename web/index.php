<?php
//Если хост локальный, то включаем режим отладки и подключаем отладочную конфигурацию
$environment = 'prod';

if (stripos($_SERVER['HTTP_HOST'], '.dev') !== false) {
    $environment = 'dev';
}

defined('YII_DEBUG') or define('YII_DEBUG', ($environment == 'dev'));
defined('YII_ENV') or define('YII_ENV', $environment);

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../config/web.php');

(new yii\web\Application($config))->run();