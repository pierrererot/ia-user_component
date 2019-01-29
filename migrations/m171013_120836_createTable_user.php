<?php

use user_component\lib\enum\UserStatus;
use yii\db\Migration;

class m171013_120836_createTable_user extends Migration
{
    private $tableName = 'user';
    private $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

    /**
     *
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(20),
            'status' => $this->integer()->notNull()->defaultValue(UserStatus\UserStatus::PENDING_REGISTRATION),
            'email' => $this->string()->notNull()->unique(),
            'logged_in_from' => $this->text(),
            'logged_in_at' => $this->dateTime(),
            'password_updated_at' => $this->dateTime(),
            'password_usage' => $this->integer(),
            'blocked_at' => $this->dateTime(),
            'registered_from' => $this->text(),
            'password_hash' => $this->text()->notNull(),
            'auth_key' => $this->text()->notNull(),
            'confirmed_at' => $this->dateTime(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $this->tableOptions);

    }

    /**
     *
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
