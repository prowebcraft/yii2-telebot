<?php

namespace prowebcraft\yii2telebot\models\base;

use Yii;
use prowebcraft\yii2telebot\models\TelegramChat;

/**
 * This is the model class for table "telegram_chat_participant".
 *
 * @property integer $id
 * @property integer $chat_id
 * @property integer $user_id
 * @property integer $status
 * @property string $joined_at
 * @property string $updated_at
 *
 * @property TelegramChat $chat
 * @property TelegramChat $user
 */
class TelegramChatParticipantBase extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'telegram_chat_participant';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['chat_id', 'user_id'], 'required'],
            [['chat_id', 'user_id', 'status'], 'integer'],
            [['joined_at', 'updated_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'chat_id' => 'Chat ID',
            'user_id' => 'User ID',
            'status' => 'Status',
            'joined_at' => 'Joined At',
            'updated_at' => 'Updated At',
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
     * Set chat_id property.
     * @param integer $chatId
     * @return $this
     */
    public function setChatId($chatId)
    {
        $this->chat_id = $chatId;
        return $this;
    }

    /**
     * Get chat_id property.
     * @return integer
     */
    public function getChatId()
    {
        return $this->chat_id;
    }

    /**
     * Set user_id property.
     * @param integer $userId
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;
        return $this;
    }

    /**
     * Get user_id property.
     * @return integer
     */
    public function getUserId()
    {
        return $this->user_id;
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
     * Set joined_at property.
     * @param string $joinedAt
     * @return $this
     */
    public function setJoinedAt($joinedAt)
    {
        $time = is_string($joinedAt) ? strtotime($joinedAt) : (is_numeric($joinedAt) ? $joinedAt : time());
        $joinedAt = date("Y-m-d H:i:s", $time);
        $this->joined_at = $joinedAt;
        return $this;
    }

    /**
     * Get joined_at property.
     * @return string
     */
    public function getJoinedAt()
    {
        return $this->joined_at;
    }

    /**
     * Set updated_at property.
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $time = is_string($updatedAt) ? strtotime($updatedAt) : (is_numeric($updatedAt) ? $updatedAt : time());
        $updatedAt = date("Y-m-d H:i:s", $time);
        $this->updated_at = $updatedAt;
        return $this;
    }

    /**
     * Get updated_at property.
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

        
    /**
     * @return \yii\db\ActiveQuery|TelegramChat     */
    public function getChat()
    {
        return $this->hasOne(TelegramChat::className(), ['id' => 'chat_id']);
    }

    /**
     * @return \yii\db\ActiveQuery|TelegramChat     */
    public function getUser()
    {
        return $this->hasOne(TelegramChat::className(), ['id' => 'user_id']);
    }

}
