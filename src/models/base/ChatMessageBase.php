<?php

namespace common\models\base;

use Yii;
use common\models\Chat;

/**
 * This is the model class for table "chat_message".
 *
 * @property string $id
 * @property string $chat_id
 * @property string $direction
 * @property integer $message_id
 * @property string $text
 * @property string $created_at
 * @property string $params
 *
 * @property Chat $chat
 */
class ChatMessageBase extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'chat_message';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['chat_id'], 'required'],
            [['message_id'], 'integer'],
            [['text', 'params'], 'string'],
            [['created_at'], 'safe'],
            [['chat_id'], 'string', 'max' => 20],
            [['direction'], 'string', 'max' => 10],
            [['chat_id', 'message_id'], 'unique', 'targetAttribute' => ['chat_id', 'message_id'], 'message' => Yii::t('app','The combination of {firstLabels} and {lastLabel} has already been taken.',['firstLabels'=>'Chat ID','lastLabel'=>'Message ID'])]
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
            'direction' => 'Direction',
            'message_id' => 'Message ID',
            'text' => 'Text',
            'created_at' => 'Created At',
            'params' => 'Params',
        ];
    }

    /**
     * Set id property.
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id property.
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set chat_id property.
     * @param string $chatId
     * @return $this
     */
    public function setChatId($chatId)
    {
        $this->chat_id = $chatId;
        return $this;
    }

    /**
     * Get chat_id property.
     * @return string
     */
    public function getChatId()
    {
        return $this->chat_id;
    }

    /**
     * Set direction property.
    * Направление
     * @param string $direction
     * @return $this
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;
        return $this;
    }

    /**
     * Get direction property.
    * Направление
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * Set message_id property.
     * @param integer $messageId
     * @return $this
     */
    public function setMessageId($messageId)
    {
        $this->message_id = $messageId;
        return $this;
    }

    /**
     * Get message_id property.
     * @return integer
     */
    public function getMessageId()
    {
        return $this->message_id;
    }

    /**
     * Set text property.
     * @param string $text
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * Get text property.
     * @return string
     */
    public function getText()
    {
        return $this->text;
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
     * @return \yii\db\ActiveQuery
     */
    public function getChat()
    {
        return $this->hasOne(Chat::className(), ['telegram_id' => 'chat_id']);
    }

}
