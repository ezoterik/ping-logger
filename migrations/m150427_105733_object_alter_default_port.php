<?php

use yii\db\Schema;
use yii\db\Migration;

class m150427_105733_object_alter_default_port extends Migration
{
    public function up()
    {
        $this->alterColumn('{{%object}}', 'port', 'SMALLINT(5) UNSIGNED NOT NULL DEFAULT "0"');
    }

    public function down()
    {
        $this->alterColumn('{{%object}}', 'port', 'SMALLINT(5) UNSIGNED NOT NULL DEFAULT "80"');
    }
}
