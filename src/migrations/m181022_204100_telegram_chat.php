<?php

namespace prowebcraft\yii2telebot\migrations;

use yii\db\Migration;

/**
 * Class m181022_204456_telegram_chats
 */
class m181022_204100_telegram_chat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('telegram_chat', [
            'id' => $this->primaryKey()->unsigned(),
            'bot_id' => $this->integer()->unsigned()->notNull(),
            'telegram_id' => $this->string(20)->notNull(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'last_message_at' => $this->dateTime()->null(),
            'name' => $this->string()->null(),
            'params' => $this->text()->null(),
        ]);
        if ($this->db->driverName !== 'sqlite') {
            $this->addForeignKey(
                'fk_telegram_chat_bot_id',
                'telegram_chat',
                'bot_id',
                'telegram_bot',
                'id',
                'CASCADE',
                'CASCADE',
            );
        }

        $this->createIndex(
            'idx_unique_bot_telegram_id',
            'telegram_chat',
            ['bot_id', 'telegram_id'],
            true,
        );

        $this->createIndex(
            'idx_telegram_id',
            'telegram_chat',
            'telegram_id',
        );

        $this->createIndex(
            'idx_last_message_at',
            'telegram_chat',
            'last_message_at',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_last_message_at', 'telegram_chat');
        $this->dropIndex('idx_telegram_id', 'telegram_chat');
        $this->dropIndex('idx_unique_bot_telegram_id', 'telegram_chat');
        if ($this->db->driverName !== 'sqlite') {
            $this->dropForeignKey('fk_telegram_chat_bot_id', 'telegram_chat');
        }
        $this->dropTable('telegram_chat');

        return true;
    }
}
