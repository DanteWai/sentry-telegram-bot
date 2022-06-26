<?php

namespace Modules\Bot\Contracts;

use Modules\Bot\Sentry\WebhookData;

abstract class TelegramBotAbstract
{
    protected string $bot_token;

    abstract public function sendWebhookNotify($chat_id, WebhookData $webhookData);

    abstract public function sendMessage($chat_id, string $message, array $buttons = []): array;
}