<?php

namespace common\models;

use prowebcraft\yii2params\Params;

class ChatMessage extends \common\models\base\ChatMessageBase
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
