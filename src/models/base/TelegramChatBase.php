<?php

namespace prowebcraft\yii2telebot\models\base;

use Yii;
use prowebcraft\yii2telebot\models\TelegramBot;
use prowebcraft\yii2telebot\models\TelegramChatMessage;

/**
 * This is the model class for table "telegram_chat".
 *
 * @property integer $id
 * @property integer $bot_id
 * @property string $telegram_id
 * @property string $name
 * @property string $params
 *
 * @property TelegramBot $bot
 * @property TelegramChatMessage[] $telegramChatMessages
 */
class TelegramChatBase extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'telegram_chat';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['bot_id', 'telegram_id'], 'required'],
            [['bot_id'], 'integer'],
            [['params'], 'safe'],
            [['telegram_id'], 'string', 'max' => 20],
            [['name'], 'string', 'max' => 255],
            [['telegram_id'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bot_id' => 'Bot ID',
            'telegram_id' => 'Telegram ID',
            'name' => 'Name',
            'params' => 'Params',
        ];
    }

    /**
     * Set id property.
     * @param integer $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id property.
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set bot_id property.
     * @param integer $botId
     * @return $this
     */
    public function setBotId($botId)
    {
        $this->bot_id = $botId;
        return $this;
    }

    /**
     * Get bot_id property.
     * @return integer
     */
    public function getBotId()
    {
        return $this->bot_id;
    }

    /**
     * Set telegram_id property.
     * @param string $telegramId
     * @return $this
     */
    public function setTelegramId($telegramId)
    {
        $this->telegram_id = $telegramId;
        return $this;
    }

    /**
     * Get telegram_id property.
     * @return string
     */
    public function getTelegramId()
    {
        return $this->telegram_id;
    }

    /**
     * Set name property.
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name property.
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set params property.
     * @param string $params
     * @return $this
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * Get params property.
     * @return string
     */
    public function getParams()
    {
        return $this->params;
    }

        
    /**
     * @return \yii\db\ActiveQuery|TelegramBot     */
    public function getBot()
    {
        return $this->hasOne(TelegramBot::className(), ['id' => 'bot_id']);
    }

    /**
     * @return \yii\db\ActiveQuery|TelegramChatMessage     */
    public function getTelegramChatMessages()
    {
        return $this->hasMany(TelegramChatMessage::className(), ['chat_id' => 'telegram_id']);
    }

}
