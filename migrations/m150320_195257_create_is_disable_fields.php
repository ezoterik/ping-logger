<?php

use yii\db\Schema;
use yii\db\Migration;

class m150320_195257_create_is_disable_fields extends Migration
{
    public function up()
    {
        $this->addColumn('{{%types}}', 'is_disable', 'TINYINT(1) UNSIGNED NOT NULL DEFAULT 0');
        $this->addColumn('{{%objects}}', 'is_disable', 'TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER status');
    }

    public function down()
    {
        $this->dropColumn('{{%types}}', 'is_disable');
        $this->dropColumn('{{%objects}}', 'is_disable');
    }
}
