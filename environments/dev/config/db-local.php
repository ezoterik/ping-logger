<?php

use yii\db\Connection as YiiDbConnection;

return [
    'class' => YiiDbConnection::class,
    'dsn' => 'mysql:host=localhost;dbname=app',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
];
