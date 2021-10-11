<?php

namespace prowebcraft\yii2telebot\models;

use prowebcraft\yii2params\Params;
use prowebcraft\yii2telebot\models\base\TelegramChatBase;

class TelegramChat extends TelegramChatBase
{
    use Params;

    public bool $cached = false;

    /**
     * @param string $telegramId
     * @return $this
     */
    public function setTelegramId($telegramId)
    {
        $telegramId = (string) $telegramId;
        return parent::setTelegramId($telegramId);
    }

}
