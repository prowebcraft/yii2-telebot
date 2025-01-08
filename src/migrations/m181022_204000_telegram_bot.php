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
        $this->createTable('telegram_bot', [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(100)->notNull()->unique(),
            'params' => $this->text()->null(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('telegram_bot');
    }
}
