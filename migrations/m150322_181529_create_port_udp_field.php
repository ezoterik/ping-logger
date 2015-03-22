<?php

use yii\db\Schema;
use yii\db\Migration;

class m150322_181529_create_port_udp_field extends Migration
{
    public function up()
    {
        $this->addColumn('{{%objects}}', 'port_udp', 'SMALLINT(6) UNSIGNED NOT NULL DEFAULT 0 AFTER port');
    }

    public function down()
    {
        $this->dropColumn('{{%objects}}', 'port_udp');
    }
}
