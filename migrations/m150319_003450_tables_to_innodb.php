<?php

use yii\db\Schema;
use yii\db\Migration;

class m150319_003450_tables_to_innodb extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{%logs}} ENGINE = InnoDB;');
        $this->execute('ALTER TABLE {{%objects}} ENGINE = InnoDB;');
        $this->execute('ALTER TABLE {{%types}} ENGINE = InnoDB;');
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{%logs}} ENGINE = MyISAM;');
        $this->execute('ALTER TABLE {{%objects}} ENGINE = MyISAM;');
        $this->execute('ALTER TABLE {{%types}} ENGINE = MyISAM;');
    }
}
