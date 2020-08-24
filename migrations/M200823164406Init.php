<?php

namespace app\migrations;

use app\components\Migration;

class M200823164406Init extends Migration
{
    public function up()
    {
        $this->createTable('{{%group}}', [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string()->notNull(),
            'is_disable' => $this->boolean(),
            'lock_at' => $this->unixTimestamp(),
        ]);

        $this->createIndex('idx-group-is_disable_lock_at', '{{%group}}', ['is_disable', 'lock_at']);

        $this->createTable('{{%object}}', [
            'id' => $this->primaryKey()->unsigned(),
            'ip' => $this->char(15)->notNull(),
            'port' => $this->smallInteger()->unsigned()->notNull()->defaultValue(0),
            'port_udp' => $this->smallInteger()->unsigned()->notNull()->defaultValue(0),
            'name' => $this->string()->notNull(),
            'address' => $this->string()->notNull()->defaultValue(''),
            'note' => $this->string()->notNull()->defaultValue(''),
            'group_id' => $this->integer()->unsigned()->notNull(),
            'status' => $this->tinyInteger()->unsigned()->notNull()->defaultValue(0),
            'avg_rtt' => $this->float()->unsigned()->notNull()->defaultValue(0),
            'is_disable' => $this->boolean(),
            'updated_at' => $this->unixTimestamp()->notNull(),
        ]);

        $this->createIndex('idx-object-group_id', '{{%object}}', 'group_id');

        $this->createTable('{{%log}}', [
            'object_id' => $this->integer()->unsigned()->notNull(),
            'event_num' => $this->tinyInteger()->unsigned()->notNull()->defaultValue(0),
            'created_at' => $this->unixTimestamp()->notNull(),
        ]);

        $this->createIndex('idx-log-object_id', '{{%log}}', 'object_id');
    }

    public function down()
    {
        echo "M200823164406Init cannot be reverted.\n";

        return false;
    }
}
