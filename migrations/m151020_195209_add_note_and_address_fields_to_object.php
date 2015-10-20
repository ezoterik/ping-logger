<?php

use yii\db\Migration;

class m151020_195209_add_note_and_address_fields_to_object extends Migration
{
    public function up()
    {
        $this->addColumn('{{%object}}', 'address', 'VARCHAR(255) NOT NULL DEFAULT "" AFTER name');
        $this->addColumn('{{%object}}', 'note', 'VARCHAR(255) NOT NULL DEFAULT "" AFTER address');
    }

    public function down()
    {
        $this->dropColumn('{{%object}}', 'address');
        $this->dropColumn('{{%object}}', 'note');
    }
}
