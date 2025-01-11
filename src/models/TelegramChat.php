<?php

namespace prowebcraft\yii2telebot\models;

use prowebcraft\yii2params\Params;
use prowebcraft\yii2telebot\models\base\TelegramChatBase;

class TelegramChat extends TelegramChatBase
{
    use Params;

    public bool $cached = false;

    const STATUS_ACTIVE = 1;
    const STATUS_LEFT = 0;
    const STATUS_KICKED = 5;

    /**
     * @param string $telegramId
     * @return $this
     */
    public function setTelegramId($telegramId)
    {
        $telegramId = (string) $telegramId;
        return parent::setTelegramId($telegramId);
    }

    /**
     * Get chat record for telegramId
     * @param string $telegramId
     * @param int $botId
     * @return $this|null
     */
    public static function findByTelegramId(string $telegramId, int $botId): ?self
    {
        return self::findOne([
            'telegram_id' => $telegramId,
            'bot_id' => $botId,
        ]);
    }

    /**
     * @param string $format The format of the mention link, either 'html' or 'markdown'.
     * @return string The mention link formatted as specified.
     */
    public function getMentionWithLink($format = 'html'): string
    {
        if ($format === 'markdown') {
            return sprintf('[%s](tg://user?id=%s)', $this->getName(), $this->getTelegramId());
        }

        return sprintf('<a href="tg://user?id=%s">%s</a>', $this->getTelegramId(), $this->getName());
    }

}
