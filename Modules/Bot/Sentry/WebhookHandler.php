<?php

namespace Modules\Bot\Sentry;


use Modules\Bot\Contracts\WebhookHandlerInterface;

class WebhookHandler implements WebhookHandlerInterface
{
    public function parseData(array $data): WebhookData
    {
        return new WebhookData();
    }
}