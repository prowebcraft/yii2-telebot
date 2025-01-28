<?php

namespace prowebcraft\yii2telebot\models\base;

use Yii;
use prowebcraft\yii2telebot\models\TelegramBot;
use prowebcraft\yii2telebot\models\TelegramChatMessage;
use prowebcraft\yii2telebot\models\TelegramChatParticipant;

/**
 * This is the model class for table "telegram_chat".
 *
 * @property integer $id
 * @property integer $bot_id
 * @property integer $status
 * @property string $telegram_id
 * @property string $type
 * @property string $created_at
 * @property string $last_message_at
 * @property string $name
 * @property string $params
 *
 * @property TelegramBot $bot
 * @property TelegramChatMessage[] $telegramChatMessages
 * @property TelegramChatParticipant[] $telegramChatParticipants
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
            [['bot_id', 'status'], 'integer'],
            [['created_at', 'last_message_at', 'params'], 'safe'],
            [['telegram_id'], 'string', 'max' => 20],
            [['type'], 'string', 'max' => 10],
            [['name'], 'string', 'max' => 255],
            [['bot_id', 'telegram_id'], 'unique', 'targetAttribute' => ['bot_id', 'telegram_id'], 'message' => Yii::t('app', 'The combination of {firstLabels} and {lastLabel} has already been taken.', ['firstLabels' => 'Bot ID', 'lastLabel' => 'Telegram ID'])]
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
            'status' => 'Status',
            'telegram_id' => 'Telegram ID',
            'type' => 'Type',
            'created_at' => 'Created At',
            'last_message_at' => 'Last Message At',
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
     * Set status property.
     * @param integer $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status property.
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
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
     * Set type property.
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type property.
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set created_at property.
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $time = is_string($createdAt) ? strtotime($createdAt) : (is_numeric($createdAt) ? $createdAt : time());
        $createdAt = date("Y-m-d H:i:s", $time);
        $this->created_at = $createdAt;
        return $this;
    }

    /**
     * Get created_at property.
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set last_message_at property.
     * @param string $lastMessageAt
     * @return $this
     */
    public function setLastMessageAt($lastMessageAt)
    {
        $time = is_string($lastMessageAt) ? strtotime($lastMessageAt) : (is_numeric($lastMessageAt) ? $lastMessageAt : time());
        $lastMessageAt = date("Y-m-d H:i:s", $time);
        $this->last_message_at = $lastMessageAt;
        return $this;
    }

    /**
     * Get last_message_at property.
     * @return string
     */
    public function getLastMessageAt()
    {
        return $this->last_message_at;
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
        return $this->hasMany(TelegramChatMessage::className(), ['user_id' => 'telegram_id']);
    }

    /**
     * @return \yii\db\ActiveQuery|TelegramChatParticipant     */
    public function getTelegramChatParticipants()
    {
        return $this->hasMany(TelegramChatParticipant::className(), ['user_id' => 'id']);
    }

}
