<?php

use yii\db\Migration;

/**
 * Class m180628_131113_createTable_blacklist
 */
class m180628_131113_createTable_blacklist extends Migration
{
    private $tableName = 'blacklist';
    private $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(20),
            'ip' => $this->text(),
            'enabled' => $this->boolean()->defaultValue(true),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ], $this->tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }

}
