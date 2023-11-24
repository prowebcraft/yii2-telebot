<?php

namespace prowebcraft\yii2telebot\migrations;

use yii\db\Migration;

/**
 * Class m181022_204456_telegram_chats
 */
class m181022_204600_chat_members_view extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE VIEW `chat_members` AS SELECT
                chat.id as chat_id,
                chat.telegram_id as telegram_chat_id,
                chat.name as chat_name,
                user.id as user_id,
                user.name as username,
                user.telegram_id as telegram_user_id,
                user.status as user_status
            FROM
                telegram_chat_participant tcp
                INNER JOIN telegram_chat chat ON tcp.chat_id = chat.id
                INNER JOIN telegram_chat user ON tcp.user_id = user.id;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("
           DROP VIEW `chat_members`
        ");

        return true;
    }

}
