<?php

use yii\db\Connection as YiiDbConnection;

return [
    'class' => YiiDbConnection::class,
    'dsn' => 'mysql:host=localhost;dbname=app',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
    'enableSchemaCache' => true,
    'schemaCacheDuration' => 60,
    'schemaCache' => 'cache',
];
