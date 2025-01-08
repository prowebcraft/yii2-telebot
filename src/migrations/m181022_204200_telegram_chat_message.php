<?php

namespace prowebcraft\yii2telebot\migrations;

use yii\db\Migration;

/**
 * Class m181022_204619_telegram_messages
 */
class m181022_204200_telegram_chat_message extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('telegram_chat_message', [
            'id' => $this->primaryKey()->unsigned(),
            'chat_id' => $this->string(20)->notNull(),
            'direction' => $this->string(10)->null(),
            'message_id' => $this->integer(11)->null(),
            'text' => $this->text()->null(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'params' => $this->text()->null(),
        ]);
        if ($this->db->driverName !== 'sqlite') {
            $this->addForeignKey(
                'fk_telegram_chat_message_chat_id',
                'telegram_chat_message',
                'chat_id',
                'telegram_chat',
                'telegram_id',
                'CASCADE',
                'CASCADE',
            );
        }

        $this->createIndex(
            'idx_telegram_chat_message_created_at',
            'telegram_chat_message',
            'created_at',
        );

        $this->createIndex(
            'idx_telegram_chat_message_chat_id_message_id',
            'telegram_chat_message',
            ['chat_id', 'message_id'],
            true,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if ($this->db->driverName !== 'sqlite') {
            $this->dropForeignKey('fk_telegram_chat_message_chat_id', 'telegram_chat_message');
        }
        $this->dropIndex('idx_telegram_chat_message_created_at', 'telegram_chat_message');
        $this->dropIndex('idx_telegram_chat_message_chat_id_message_id', 'telegram_chat_message');
        $this->dropTable('telegram_chat_message');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181022_204619_telegram_messages cannot be reverted.\n";

        return false;
    }
    */
}
