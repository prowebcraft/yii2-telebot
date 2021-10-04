<?php

namespace prowebcraft\yii2telebot\models;

use prowebcraft\yii2params\Params;

class TelegramChatMessage extends \common\models\base\ChatMessageBase
{
    use Params;

    const DIRECTION_FROM = 'from';
    const DIRECTION_TO = 'to';

    /**
     * @param string $chatId
     * @return $this
     */
    public function setChatId($chatId)
    {
        $chatId = (string)$chatId;
        return parent::setChatId($chatId);
    }

}
