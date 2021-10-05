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
        $this->execute("
            ALTER TABLE `telegram_chat` 
            ADD COLUMN `type` varchar(10) NULL AFTER `telegram_id`,
            ADD INDEX `type`(`type`);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("
            ALTER TABLE `telegram_chat` 
            DROP COLUMN `type`
            ;
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
