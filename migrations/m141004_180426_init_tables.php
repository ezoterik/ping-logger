<?php

use yii\db\Schema;
use yii\db\Migration;

class m141004_180426_init_tables extends Migration
{
    public function up()
    {
        $this->createTable('{{%types}}', [
            'id' => 'SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT',
            'name' => 'VARCHAR(255) NOT NULL DEFAULT ""',
            'PRIMARY KEY ([[id]])',
        ], 'ENGINE=MyISAM DEFAULT CHARSET=utf8');

        $this->createTable('{{%objects}}', [
            'id' => Schema::TYPE_PK,
            'ip' => 'CHAR(15) NOT NULL',
            'port' => 'SMALLINT(5) UNSIGNED NOT NULL DEFAULT "80"',
            'name' => 'VARCHAR(255) NOT NULL DEFAULT ""',
            'type_id' => 'SMALLINT(5) UNSIGNED NOT NULL DEFAULT "0"',
            'status' => 'TINYINT(2) UNSIGNED NOT NULL DEFAULT "0"',
            'updated' => Schema::TYPE_DATETIME . ' NOT NULL',
            'KEY [[type_id]] ([[type_id]])',
        ], 'ENGINE=MyISAM DEFAULT CHARSET=utf8');

        $this->createTable('{{%logs}}', [
            'object_id' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
            'event_num' => 'TINYINT(3) UNSIGNED NOT NULL DEFAULT "0"',
            'created' => Schema::TYPE_DATETIME . ' NOT NULL',
            'KEY [[object_id]] ([[object_id]])',
        ], 'ENGINE=MyISAM DEFAULT CHARSET=utf8');
    }

    public function down()
    {
        echo "m141004_180426_init_tables cannot be reverted.\n";

        return false;
    }
}
