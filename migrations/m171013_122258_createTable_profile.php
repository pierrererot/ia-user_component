<?php

use yii\db\Migration;

class m171013_122258_createTable_profile extends Migration
{
    private $tableName = 'profile';
    private $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

    /**
     *
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(20),
            'user_id' => $this->integer(20)->notNull(),
            'first_name' => $this->text(),
            'last_name' => $this->text(),
            'cellphone' => $this->text(),
            'landline_phone' => $this->text(),
            'fax' => $this->text(),
            'created_at' => $this->datetime(),
            'updated_at' => $this->datetime(),
        ], $this->tableOptions);

        $this->addForeignKey("fk-$this->tableName-user", $this->tableName, 'user_id', 'user', 'id', 'CASCADE');
        $this->createIndex("unique-$this->tableName-user_id", $this->tableName, 'user_id', true);
    }

    /**
     *
     */
    public function safeDown()
    {
        $this->dropTable('profile');
    }
}
