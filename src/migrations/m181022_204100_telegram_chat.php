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
        $this->execute("
            CREATE TABLE `telegram_chat` (
                `id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
                `bot_id`  int(10) UNSIGNED NOT NULL,
                `telegram_id`  varchar(20) NOT NULL ,
                `name`  varchar(255) NULL ,
                `params`  longtext NULL ,
            PRIMARY KEY (`id`),
            FOREIGN KEY (`bot_id`) REFERENCES `telegram_bot` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            UNIQUE INDEX (`telegram_id`) 
            );
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181022_204456_telegram_chats cannot be reverted.\n";

        return false;
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
