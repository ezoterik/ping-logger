<?php

use yii\db\Schema;
use yii\db\Migration;

class m150425_154239_group_add_lock_field extends Migration
{
    public function up()
    {
        $this->addColumn('{{%group}}', 'lock_date', 'DATETIME NOT NULL');
        $this->createIndex('is_disable_and_lock_date', '{{%group}}', ['is_disable', 'lock_date']);
    }

    public function down()
    {
        $this->dropColumn('{{%group}}', 'lock_date');
    }
}
