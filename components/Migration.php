<?php

namespace app\components;

class Migration extends \yii\db\Migration
{
    public function createTable($table, $columns, $options = null)
    {
        if ($options === null) {
            switch ($this->db->driverName) {
                case 'mysql':
                    // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
                    $options = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
                    break;
            }
        }

        parent::createTable($table, $columns, $options);
    }

    public function boolean()
    {
        return parent::boolean()->unsigned()->notNull()->defaultValue('0');
    }

    public function unixTimestamp()
    {
        return $this->integer()->unsigned();
    }
}
