<?php
/**
 * Telegram Bot for Yii2
 * User: Andrey Mistulov
 * Email: prowebcraft@gmail.com
 * Date: 04.10.2021
 */

namespace prowebcraft\yii2telebot;

use Exception;
use Prowebcraft\Dot;
use Prowebcraft\Telebot\Clients\Basic;
use Prowebcraft\Telebot\Telebot;
use prowebcraft\yii2telebot\models\TelegramBot;
use prowebcraft\yii2telebot\models\TelegramChat;
use prowebcraft\yii2telebot\models\TelegramChatMessage;
use prowebcraft\yii2telebot\models\TelegramChatParticipant;
use Symfony\Component\Translation\Loader\CsvFileLoader;
use Symfony\Component\Translation\Translator;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Client;
use TelegramBot\Api\Types\CallbackQuery;
use TelegramBot\Api\Types\Chat;
use TelegramBot\Api\Types\ChatMemberUpdated;
use TelegramBot\Api\Types\ReplyParameters;
use TelegramBot\Api\Types\User;
use yii\log\Logger;

class YiiBot extends Telebot
{
    public ?int $botId = null;
    protected ?TelegramBot $botModel = null;
    protected ?string $botToken = null;
    protected ?string $botName = null;
    protected ?array $botParams = null;
    /** @var array|TelegramChat[]  */
    protected array $chats = [];
    /** @var array|TelegramChatParticipant[] */
    protected array $participants = [];
    protected ?TelegramChat $chat = null;
    protected ?TelegramChat $user = null;

    public function __construct(string $name)
    {
        if (!$botConfig = \Yii::$app->params['bots'][$name] ?? null) {
            throw new \InvalidArgumentException('Please add bot '.$name.' config in params');
        }
        if (empty($botConfig['token'])) {
            throw new \InvalidArgumentException('Please fill bot '.$name.' token in params');
        }
        $this->botParams = $botConfig;
        $this->botToken = $this->getBotParam('token');
        $root = dirname(__DIR__, 4);
        $botOptions = array_merge([
            'appDir' => $root . DIRECTORY_SEPARATOR . 'console' . DIRECTORY_SEPARATOR . 'runtime',
            'runtimeDir' => $root . DIRECTORY_SEPARATOR . 'console' . DIRECTORY_SEPARATOR . 'runtime',
        ], $botConfig['options'] ?? []);
        parent::__construct($name, null, null, null, $botOptions);
        $this->botName = $name;
        //init bot model
        if (!$botModel = TelegramBot::findOne(['name' => $name])) {
            $botModel = new TelegramBot([
                'name' => $name
            ]);
            $botModel->setParam('className', static::class);
            if (!empty($botConfig['default_bot_config']) && is_array($botConfig['default_bot_config'])) {
                $botModel->setParam('config', $botConfig['default_bot_config']);
            }
            $botModel->save();
        }
        $this->botModel = $botModel;
        $this->botId = $botModel->id;
    }

    /**
     * @inheritDoc
     */
    protected function proceedRun(): bool
    {
        // Check database connection on each iteration
        \Yii::$app->db->createCommand("SELECT 1")->query();
        return parent::proceedRun();
    }


    /**
     * Get Bot Yii2 Param configuration
     * @param string $key
     * @param mixed|null $default
     * @return array|mixed|null
     */
    public function getBotParam(string $key, mixed $default = null)
    {
        return Dot::getValue($this->botParams, $key, $default);
    }

    /**
     * Perform init (load database, restore replies, config loggers etc)
     * @throws Exception
     */
    public function init()
    {
        //Restore Waiting Messages
        $this->restoreReplies();

        /** @var BotApi|Client $bot */
        $bot = new Basic($this->botToken, null, $this->getConfig('config.proxy'));
        $this->telegram = $bot;

        //Init Translations
        $this->translator = new Translator('en_US');
        //Add Default Resourse
        $this->translator->addLoader('csv', new CsvFileLoader());
        $filesDir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'telebot' . DIRECTORY_SEPARATOR . 'files';
        $this->translator->addResource('csv', $filesDir . DIRECTORY_SEPARATOR . 'locale'
            . DIRECTORY_SEPARATOR . 'system.ru.csv', 'ru');
        $this->configTranslations($this->translator);
    }

    /**
     * @inheritDoc
     */
    protected function log($message, $level = \Monolog\Logger::INFO, $extra = [])
    {
        $yiiLevel = Logger::LEVEL_INFO;
        switch ($level) {
            case \Monolog\Logger::ERROR:
            case \Monolog\Logger::EMERGENCY:
            case \Monolog\Logger::CRITICAL:
            case \Monolog\Logger::ALERT:
                $yiiLevel = Logger::LEVEL_ERROR;
                break;
            case \Monolog\Logger::NOTICE:
            case \Monolog\Logger::WARNING:
                $yiiLevel = Logger::LEVEL_WARNING;
                break;
            case \Monolog\Logger::DEBUG:
                $yiiLevel = Logger::LEVEL_TRACE;
                break;
        }
        \Yii::getLogger()->log($message, $yiiLevel, 'bot.'.$this->botName);
    }


    /**
     * @inheritDoc
     */
    public function getConfig($key, $default = null)
    {
        return $this->botModel->getParam($key, $default);
    }

    /**
     * @inheritDoc
     */
    public function setConfig($key, $value, $save = true)
    {
        $this->botModel->setParam($key, $value);
        if ($save) {
            $this->botModel->save();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function deleteConfig($key, $save = true)
    {
        $this->botModel->unsetParam($key);
        if ($save) {
            $this->botModel->save();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addConfig($key, $value, $save = true)
    {
        $this->botModel->addParam($key, $value);
        if ($save) {
            $this->botModel->save();
        }

        return $this;
    }

    /**
     * Получение модели чата
     * @param $id
     * @param bool $reload
     * @param bool $create
     * @return TelegramChat|null
     */
    public function getChat($id, bool $reload = false, bool $create = true): ?TelegramChat
    {
        if (!$reload && isset($this->chats[$id])) {
            $this->chats[$id]->cached = true;

            return $this->chats[$id];
        }

        if (!($chat = TelegramChat::findOne([
            'bot_id' => $this->botId,
            'telegram_id' => $id
        ]))) {
            if (!$create) {
                return null;
            }
            $chat = new TelegramChat();
            $chat
                ->setBotId($this->botId)
                ->setTelegramId($id)
                ->setCreatedAt(time())
                ->save();
            if ($errors = $chat->getErrors()) {
                $this->error('Error saving chat to database: %s', $errors);
            }
        }
        $this->chats[$id] = $chat;

        return $chat;
    }

    /**
     * Получение модели участника чата
     * @param int $chatId
     * @param int $userId
     * @param bool $reload
     * @return TelegramChatParticipant
     */
    public function getParticipant(int $chatId, int $userId, bool $reload = false): TelegramChatParticipant
    {
        $key = "$chatId.$userId";
        if (!$reload && isset($this->participants[$key])) {
            $this->participants[$key]->cached = true;

            return $this->participants[$key];
        }

        if (!($participant = TelegramChatParticipant::findOne([
            'chat_id' => $chatId,
            'user_id' => $userId,
        ]))) {
            $participant = new TelegramChatParticipant();
            $participant
                ->setChatId($chatId)
                ->setUserId($userId)
                ->setJoinedAt(time())
                ->setStatus(TelegramChatParticipant::STATUS_ACTIVE)
                ->save();
            if ($errors = $participant->getErrors()) {
                $this->error('Error saving chat participant to database: %s', $errors);
            }
        }
        $this->participants[$key] = $participant;

        return $participant;
    }

    /**
     * @inheritDoc
     */
    protected function onMigrateToSuperGroup(int $oldId = null)
    {
        if ($chat = $this->chats[$oldId] ?? $this->getChat($oldId, create: false)) {
            $chat->setTelegramId($this->getChatId())
                ->setType('supergroup')
                ->setParam('migrated_from', $oldId)
                ->save()
            ;
        }
    }

    /**
     * @inheritDoc
     */
    protected function onNewChatMember(User $user)
    {
        $telegramUser = $this->saveUserInfo($user);
        if ($this->chat && !$user->isBot()) {
            $participant = $this->getParticipant($this->chat->getId(), $telegramUser->getId());
            $participant->setStatus(TelegramChatParticipant::STATUS_ACTIVE)->save();
        }
    }

    /**
     * @inheritDoc
     */
    protected function onChatMemberLeft(User $user)
    {
        if ($this->chat && $this->user && !$user->isBot()) {
            $telegramUser = $this->getChat($user->getId());
            $participant = $this->getParticipant($this->chat->getId(), $telegramUser->getId());
            $participant->setStatus(TelegramChatParticipant::STATUS_LEFT)->save();
        }
    }

    /**
     * @inheritDoc
     */
    protected function onBotKick(Chat $chat)
    {
        parent::onBotKick($chat);
        $chat = $this->getChat($chat->getId());
        $chat->setStatus($chat::STATUS_KICKED)->save();
    }

    /**
     * @inheritDoc
     */
    protected function onBotChatMemberStatusChange(ChatMemberUpdated $botChatMember, string $from, string $to)
    {
        parent::onBotChatMemberStatusChange($botChatMember, $from, $to);
        if ($to !== 'left') {
            if ($chat = $this->getChat($botChatMember->getChat()->getId(), create: false)) {
                $chat->setParam('bot_status', $to)->setStatus($chat::STATUS_ACTIVE)->save();
            }
        }
    }


    /**
     * @param \TelegramBot\Api\Types\Update $update
     */
    public function handleUpdate($update)
    {
        $this->update = $update;
        $this->chat = $this->user = null;
        if (($chatId = $this->getChatId())
            && ($context = $this->getContext())
            && ($message = $this->getMessageFromContext($context))
            && !$message->getMigrateFromChatId()
            && !$message->getMigrateToChatId()
        ) {
            try {
                $chat = $this->getChat($chatId);
                $this->chat = $chat;
                if (!$user = $context->getFrom() ?: $message->getFrom()) {
                    return;
                }
                if ($this->isChatPrivate()) {
                    $this->saveUserInfo($user);
                    $this->user = $chat;
                } else {
                    // group chat
                    $userModel = $this->saveUserInfo($user);
                    $this->user = $userModel;
                    $participant = $this->getParticipant($chat->id, $userModel->id);
                    $chatName = $message?->getChat()->getTitle();
                    $chat->setName($chatName);
                }
                $chat->setLastMessageAt(time())
                    ->setType($this->getChatType())
                    ->save();

                if ($context instanceof CallbackQuery) {
                    $text = sprintf('[Callback Data] %s', $context->getData());
                } else {
                    $text = $message?->getText();
                }

                $chatMessage = new TelegramChatMessage();
                $chatMessage
                    ->setChatId($chatId)
                    ->setUserId($userModel?->telegram_id ?? $chatId)
                    ->setMessageId($this->getMessageId())
                    ->setText($text)
                    ->setDirection(TelegramChatMessage::DIRECTION_FROM)
                    ->setParams($update->toJson(true))
                    ->setCreatedAt(time())
                    ->save();
            } catch (\Throwable $e) {
                $this->error('Error saving chat message: %s', $e->getMessage());
            }
        }

        parent::handleUpdate($update);
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
        $allowChunks = true,
        ?ReplyParameters $replyParameters = null,
    ) {
        $chatMessage = null;
        try {
            $this->getChat($to);
            $chatMessage = new TelegramChatMessage();
            $chatMessage
                ->setChatId($to)
                ->setText($message)
                ->setDirection(TelegramChatMessage::DIRECTION_TO)
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
        if ($chatId === null) {
            $chatId = $this->getChatId();
        }
        if ($chatId) {
            if ($chat = $this->getChat($chatId, create: false)) {
                return $chat->getParam($key, $default);
            }
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

    /**
     * Save user info
     * @param User $user
     * @return TelegramChat
     */
    protected function saveUserInfo(User $user): TelegramChat
    {
        $userId = $user->getId();
        $userModel = $this->getChat($userId);
        $fromName = $user->getFirstName()
            . ($user->getLastName() ? ' ' . $user->getLastName() : '');
        $fromName .= ($user->getUsername() ? ' @' . $user->getUsername() : '');
        if ($userModel->getName() !== $fromName) {
            $userModel->setType('private')
                ->setName($fromName)
                ->setParam('user', [
                    'firstname' => $user->getFirstName(),
                    'lastname' => $user->getLastName(),
                    'username' => $user->getUsername(),
                    'lang' => $user->getLanguageCode(),
                ])
                ->save();
        }

        return $userModel;
    }

    /**
     * Return current chat session
     * @return TelegramChat|null
     */
    public function getCurrentChat(): ?TelegramChat
    {
        return $this->chat;
    }

    /**
     * Return current bot model
     * @return TelegramBot
     */
    public function getBotModel(): TelegramBot
    {
        return $this->botModel;
    }

}
