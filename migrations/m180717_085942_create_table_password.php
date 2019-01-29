<?php

use yii\db\Migration;

/**
 * Class m180717_085942_create_table_password
 */
class m180717_085942_create_table_password extends Migration
{
    private $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $migrationTools = new \app\modules\ia\migrations\MigrationTools();
        $this->createTable('password', array_merge(
            [
                'id' => $this->primaryKey(20),
                'user_id' => $this->integer(20),
                'password_hash' => $this->string(60),
            ],
            $migrationTools->extraColumns()
        ), $this->tableOptions);

        $migrationTools->foreignKeyExtra('password');
        $this->addForeignKey(
            'fk-password-user_id',
            'password',
            'user_id',
            'user',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('password');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180717_085942_create_table_password cannot be reverted.\n";

        return false;
    }
    */
}
