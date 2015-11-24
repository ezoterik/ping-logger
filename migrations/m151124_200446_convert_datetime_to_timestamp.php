<?php

use yii\db\Migration;

class m151124_200446_convert_datetime_to_timestamp extends Migration
{
    public function up()
    {
        $this->alterColumn('{{%group}}', 'lock_date', 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->alterColumn('{{%log}}', 'created', 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->alterColumn('{{%object}}', 'updated', 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
    }

    public function down()
    {
        $this->alterColumn('{{%group}}', 'lock_date', 'DATETIME NOT NULL');
        $this->alterColumn('{{%log}}', 'created', 'DATETIME NOT NULL');
        $this->alterColumn('{{%object}}', 'updated', 'DATETIME NOT NULL');
    }
}
