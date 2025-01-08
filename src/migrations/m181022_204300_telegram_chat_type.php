<?php

namespace prowebcraft\yii2telebot\migrations;

use yii\db\Migration;

/**
 * Class m181022_204456_telegram_chats
 */
class m181022_204300_telegram_chat_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            'telegram_chat',
            'type',
            $this->string(10)->null()->after('telegram_id'),
        );
        $this->createIndex(
            'idx-telegram_chat-type',
            'telegram_chat',
            'type',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('telegram_chat', 'type');
        return true;
    }
}
