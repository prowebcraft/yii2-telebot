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
        $defaultValue = 'CURRENT_TIMESTAMP';
        if ($this->db->driverName !== 'sqlite') {
            $defaultValue .= ' ON UPDATE CURRENT_TIMESTAMP';
        }
        $this->createTable('telegram_chat_participant', [
            'id' => $this->primaryKey()->unsigned(),
            'chat_id' => $this->integer()->unsigned()->notNull(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'status' => $this->tinyInteger()->null(),
            'joined_at' => $this->dateTime()->null(),
            'updated_at' => $this->dateTime()->null()->defaultExpression($defaultValue),
        ]);

        if ($this->db->driverName !== 'sqlite') {
            $this->addForeignKey(
                'fk-telegram_chat_participant-chat_id',
                'telegram_chat_participant',
                'chat_id',
                'telegram_chat',
                'id',
                'CASCADE',
                'CASCADE',
            );

            $this->addForeignKey(
                'fk-telegram_chat_participant-user_id',
                'telegram_chat_participant',
                'user_id',
                'telegram_chat',
                'id',
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
        $this->dropTable('telegram_chat_participant');
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
