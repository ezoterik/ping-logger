<?php

use yii\base\Event;
use yii\db\Connection;

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=app',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
    'on afterOpen' => function (Event $event) {
        /** @var Connection $sender */
        $sender = $event->sender;
        $sender->createCommand('SET time_zone = "+00:00"')->execute();
    },
    'enableSchemaCache' => true,
    'schemaCacheDuration' => 60,
    'schemaCache' => 'cache',
];
