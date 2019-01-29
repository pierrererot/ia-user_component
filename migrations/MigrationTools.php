<?php
/**
 * Created by PhpStorm.
 * User: pignolet
 * Date: 26/04/2017
 * Time: 16:00
 */

namespace MigrationTools;


use yii\db\Migration;

class MigrationTools extends Migration
{
    /**
     * @return array
     */
    public function extraColumns()
    {
        return [
            'created_at' => self::timestamp(),
            'created_by' => self::integer(),
            'updated_at' => self::timestamp(),
            'updated_by' => self::integer(),
        ];
    }

    /**
     * @param string $table
     */
    public function foreignKeyExtra($table)
    {
        self::addForeignKey(
            "fk-$table-created_by",
            $table,
            'created_by',
            'user',
            'id',
            'SET NULL',
            'CASCADE'
        );
        self::addForeignKey(
            "fk-$table-updated_by",
            $table,
            'updated_by',
            'user',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * @param string $table
     */
    public function dropForeignKeyExtra($table)
    {
        self::dropForeignKey("fk-$table-created_by", $table);
        self::dropForeignKey("fk-$table-updated_by", $table);
    }
}