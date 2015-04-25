<?php

use yii\db\Schema;
use yii\db\Migration;

class m150425_013706_create_avg_rtt_field extends Migration
{
    public function up()
    {
        $this->addColumn('{{%objects}}', 'avg_rtt', 'FLOAT UNSIGNED NOT NULL DEFAULT "0" AFTER status');
    }

    public function down()
    {
        $this->dropColumn('{{%objects}}', 'avg_rtt');
    }
}
