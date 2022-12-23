<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `{{%orders}}`.
 */
class m221221_083724_create_orders_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('orders', [
            'id' => $this->primaryKey(),
            'user_name' => $this->string(100)->notNull(),
            'user_phone' => $this->string(20)->notNull(),
            'warehouse_id' => $this->bigInteger()->notNull(),
            'status' => $this->tinyInteger()->notNull(),
            'items_count' => $this->smallInteger()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('orders');
    }
}
