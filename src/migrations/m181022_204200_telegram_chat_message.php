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
        $this->execute("
        CREATE TABLE `telegram_chat_message` (
            `id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `chat_id`  varchar(20) NOT NULL ,
            `direction` varchar(10) null comment 'Направление',
            `message_id`  int(11) NULL ,
            `text`  text NULL ,
            `params`  longtext NULL ,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`chat_id`) REFERENCES `telegram_chat` (`telegram_id`) ON DELETE CASCADE ON UPDATE CASCADE,
        UNIQUE INDEX (`chat_id`, `message_id`) 
        );
      ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181022_204619_telegram_messages cannot be reverted.\n";

        return false;
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
