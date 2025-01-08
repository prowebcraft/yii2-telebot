<?php

namespace prowebcraft\yii2telebot\migrations;

use yii\db\Migration;

/**
 * Class m181022_204456_telegram_chats
 */
class m181022_204500_telegram_chat_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            'telegram_chat',
            'status',
            $this->tinyInteger()->null()->defaultValue(1)->after('bot_id'),
        );
        $this->createIndex(
            'idx-telegram_chat-status',
            'telegram_chat',
            'status',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('telegram_chat', 'status');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181022_204456_telegram_chats cannot be reverted.\n";

        return false;
    }
    */
}
