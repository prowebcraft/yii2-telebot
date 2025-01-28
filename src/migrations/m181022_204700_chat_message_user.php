<?php

namespace prowebcraft\yii2telebot\migrations;

use yii\db\Migration;

/**
 * Class m181022_204456_telegram_chats
 */
class m181022_204700_chat_message_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            'telegram_chat_message',
            'user_id',
            $this->string(20)->null()->after('chat_id'),
        );
        if ($this->db->driverName !== 'sqlite') {
            $this->addForeignKey(
                'fk_telegram_chat_message_user_id',
                'telegram_chat_message',
                'user_id',
                'telegram_chat',
                'telegram_id',
                'CASCADE',
                'CASCADE',
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('telegram_chat_message', 'user_id');
        return true;
    }

}
