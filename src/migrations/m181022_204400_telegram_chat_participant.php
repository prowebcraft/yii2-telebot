<?php

namespace prowebcraft\yii2telebot\migrations;

use yii\db\Migration;

/**
 * Class m181022_204456_telegram_chats
 */
class m181022_204400_telegram_chat_participant extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE `telegram_chat_participant`  (
              `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
              `chat_id` int(10) UNSIGNED NOT NULL,
              `user_id` int(10) UNSIGNED NOT NULL,
              `status` tinyint(10) NULL,
              `joined_at` datetime NULL,
              `updated_at` datetime NULL ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              FOREIGN KEY (`chat_id`) REFERENCES `telegram_chat` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              FOREIGN KEY (`user_id`) REFERENCES `telegram_chat` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            );
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("
            DROP TABLE telegram_chat_participant;
        ");

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
