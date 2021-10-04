<?php

namespace common\models;

use prowebcraft\yii2params\Params;

class Chat extends \common\models\base\ChatBase
{
    use Params;

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
