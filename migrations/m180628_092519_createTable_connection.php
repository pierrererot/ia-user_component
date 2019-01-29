<?php

use yii\db\Migration;

/**
 * Class m180628_092519_createTable_connection
 */
class m180628_092519_createTable_connection extends Migration
{
    public $db = 'dbLog';
    private $tableName = 'connection';
    private $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
    private $dbComponent = 'dbLog';

    /**
     *
     */
    public function init()
    {
        $this->db = $this->dbComponent;
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(20),
            'status' => $this->integer(),
            'username' => $this->text(),
            'from_ip' => $this->text(),
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
