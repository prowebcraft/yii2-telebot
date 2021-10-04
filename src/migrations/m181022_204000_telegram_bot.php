<?php

namespace prowebcraft\yii2telebot\migrations;

use yii\db\Migration;

/**
 * Class m181022_204456_telegram_chats
 */
class m181022_204000_telegram_bot extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE `telegram_bot` (
                `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
                `name`  varchar(100) NOT NULL,
                `params`  longtext NULL,
            PRIMARY KEY (`id`),
            UNIQUE INDEX (`name`) 
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
