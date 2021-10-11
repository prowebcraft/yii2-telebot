<?php

namespace prowebcraft\yii2telebot\models;

class TelegramChatParticipant extends \prowebcraft\yii2telebot\models\base\TelegramChatParticipantBase
{
    public bool $cached = false;

    const STATUS_ACTIVE = 1;
    const STATUS_LEFT = 0;
    const STATUS_KICKED = 5;

}