<?php

namespace Modules\Bot;

use Modules\Bot\Sentry\WebhookData;
use Modules\Contracts\TelegramBotAbstract;

class TelegramBot extends TelegramBotAbstract
{

    public function sendWebhookNotify($chat_id, WebhookData $webhookData)
    {
    }
}