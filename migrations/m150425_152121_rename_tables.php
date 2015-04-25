<?php

use yii\db\Schema;
use yii\db\Migration;

class m150425_152121_rename_tables extends Migration
{
    public function up()
    {
        $this->renameTable('{{%logs}}', '{{%log}}');
        $this->renameTable('{{%types}}', '{{%group}}');

        $this->renameTable('{{%objects}}', '{{%object}}');
        $this->renameColumn('{{%object}}', 'type_id', 'group_id');
        $this->dropIndex('type_id', '{{%object}}');
        $this->createIndex('group_id', '{{%object}}', 'group_id');
    }

    public function down()
    {
        $this->renameTable('{{%log}}', '{{%logs}}');
        $this->renameTable('{{%group}}', '{{%types}}');

        $this->renameColumn('{{%object}}', 'group_id', 'type_id');
        $this->dropIndex('group_id', '{{%object}}');
        $this->createIndex('type_id', '{{%object}}', 'type_id');
        $this->renameTable('{{%object}}', '{{%objects}}');
    }
}
