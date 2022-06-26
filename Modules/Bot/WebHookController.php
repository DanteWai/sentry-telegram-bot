<?php

namespace Modules\Bot;

use Modules\Bot\Contracts\TelegramMessageHandlerInterface;
use Modules\Bot\Contracts\WebhookHandlerInterface;
use Modules\Bot\Sentry\WebhookData;
use System\BaseController;

class WebHookController extends BaseController
{
    private WebhookHandlerInterface $sentryHandler;
    private TelegramMessageHandlerInterface $telegramMessageHandler;

    public function __construct(
        WebhookHandlerInterface $sentryHandler,
        TelegramMessageHandlerInterface $telegramMessageHandler
    )
    {
        $this->sentryHandler = $sentryHandler;
        $this->telegramMessageHandler = $telegramMessageHandler;
        header('Content-Type: application/json; charset=utf-8');
    }

    /**
     * Обработчик данных присланных sentry
     * @return void
     */
    public function sentry()
    {
        $this->sentryHandler->handle($this->env['post']);
        http_response_code(200);
    }

    /**
     * Обработчик сообщений из телеграм
     * @return void
     */
    public function telegram(){
        $this->telegramMessageHandler->handle($this->env['post']);
        http_response_code(200);
    }
}