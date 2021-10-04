<?php
/**
 * Telegram Bot for Yii2
 * User: Andrey Mistulov
 * Email: prowebcraft@gmail.com
 * Date: 04.10.2021
 */

namespace common\models;

use Prowebcraft\Telebot\Telebot;

class YiiBot extends Telebot
{
    protected ?int $botId = null;
    protected ?string $botName = null;
    protected $chats = [];

    public function __construct(string $name)
    {
        if (!$botConfig = \Yii::$app->params['bots'][$name] ?? null) {
            throw new \InvalidArgumentException('Please add bot '.$name.' config in params');
        }
        if (!isset($botConfig['token'])) {
            throw new \InvalidArgumentException('Please fill bot '.$name.' token in params');
        }
        $botOptions = array_merge([], $botConfig['options'] ?? []);
        parent::__construct($name, null, null, null, $botOptions);

        $this->botName = $name;
    }


    /**
     * Получение модели чата
     * @param $id
     * @param bool $reload
     * @return Chat|null
     */
    public function getChat($id, $reload = false)
    {
        if (!$reload && isset($this->chats[$id]))
            return $this->chats[$id];
        if (!($chat = Chat::findOne(['telegram_id' => $id]))) {
            $chat = new Chat();
            $chat
                ->setTelegramId($id)
                ->setCreatedAt(time())
                ->save();
        }
        $this->chats[$id] = $chat;
        return $chat;
    }

    /**
     * @param \TelegramBot\Api\Types\Update $update
     */
    public function handleUpdate($update)
    {
        parent::handleUpdate($update);

        if ($chatId = $this->getChatId()) {
            try {
                $chat = $this->getChat($chatId);
                $message = $this->getContext();
                $user = $message->getFrom();
                $chat->setLastMessageAt(time())
                    ->setName($this->getFromName($message, true))
                    ->setParam('user', [
                        'firstname' => $user->getFirstName(),
                        'lastname' => $user->getLastName(),
                        'username' => $user->getUsername(),
                        'lang' => $user->getLanguageCode(),
                    ])
                    ->save();
                $message = new ChatMessage();
                $message
                    ->setChatId($chatId)
                    ->setMessageId($this->getMessageId())
                    ->setText($update->getMessage() ? $update->getMessage()->getText() : null)
                    ->setDirection(ChatMessage::DIRECTION_FROM)
                    ->setParams($update->toJson(true))
                    ->setCreatedAt(time())
                    ->save();
            } catch (\Throwable $e) {
                $this->error('Error saving chat message: %s', $e->getMessage());
            }
        }
    }

    /**
     * @param int|string $to
     * @param string $message
     * @param string $parse
     * @param bool $disablePreview
     * @param null $replyToMessageId
     * @param null $replyMarkup
     * @param bool $disableNotification
     * @param bool $allowChunks
     * @return \TelegramBot\Api\Types\Message
     * @throws \TelegramBot\Api\Exception
     * @throws \TelegramBot\Api\InvalidArgumentException
     */
    public function sendMessage(
        $to,
        $message,
        $parse = 'HTML',
        $disablePreview = false,
        $replyToMessageId = null,
        $replyMarkup = null,
        $disableNotification = false,
        $allowChunks = true
    ) {
        $chatMessage = null;
        try {
            $this->getChat($to);
            $chatMessage = new ChatMessage();
            $chatMessage
                ->setChatId($to)
                ->setText($message)
                ->setDirection(ChatMessage::DIRECTION_TO)
                ->setParams([
                    'parse' => $parse,
                    'disable_preview' => $disablePreview,
                    'reply_to_message_id' => $replyToMessageId,
                    'replyMarkup' => $replyMarkup,
                    'disable_notification' => $disableNotification,
                    'allow_chunks' => $allowChunks
                ])
                ->save();
        } catch (\Throwable $e) {
            $this->error('Error saving outgoing chat message: %s', $e->getMessage());
        }
        $result = parent::sendMessage($to, $message, $parse, $disablePreview, $replyToMessageId, $replyMarkup, $disableNotification, $allowChunks);
        if ($chatMessage) {
            $chatMessage->setMessageId($result->getMessageId())->save();
        }
        return $result;
    }

    /**
     * Получить конфигурацию чата
     * @param $key
     * @param null $default
     * @param null $chatId
     * @return mixed
     */
    public function getChatConfig($key, $default = null, $chatId = null)
    {
        if ($chatId === null) $chatId = $this->getChatId();
        if ($chatId) {
            $chat = $this->getChat($chatId);
            return $chat->getParam($key, $default);
        }
        return false;
    }

    /**
     * Установить конфигурацию чата
     * @param $key
     * @param $value
     * @param bool $save
     * @param null $chatId
     * @return mixed
     */
    public function setChatConfig($key, $value, $save = true, $chatId = null)
    {
        if ($chatId === null) $chatId = $this->getChatId();
        if ($chatId) {
            $chat = $this->getChat($chatId);
            $chat->setParam($key, $value);
            if ($save)
                $chat->save();
            return $this;
        }
        return false;
    }

    /**
     * @param $key
     * @param $value
     * @param bool $save
     * @param null $chatId
     * @return mixed
     */
    public function addChatConfig($key, $value, $save = true, $chatId = null)
    {
        if ($chatId === null) $chatId = $this->getChatId();
        if ($chatId) {
            $chat = $this->getChat($chatId);
            $chat->addParam($key, $value);
            if ($save)
                $chat->save();
            return $this;
        }
        return false;
    }

    /**
     * @param $key
     * @param bool $save
     * @param null $chatId
     * @return mixed
     */
    public function deleteChatConfig($key, $save = true, $chatId = null)
    {
        if ($chatId === null) $chatId = $this->getChatId();
        if ($chatId) {
            $chat = $this->getChat($chatId);
            $chat->unsetParam($key);
            if ($save)
                $chat->save();
            return $this;
        }
        return false;
    }

}
