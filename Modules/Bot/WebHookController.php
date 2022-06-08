<?php

namespace Modules\Bot;

use Modules\Bot\Contracts\WebhookHandlerInterface;
use System\BaseController;

class WebHookController extends BaseController
{
    private WebhookHandlerInterface $sentryHandler;

    public function __construct(
        WebhookHandlerInterface $sentryHandler
    )
    {
        $this->sentryHandler = $sentryHandler;
    }

    public function sentry()
    {
        $this->sentryHandler->parseData($this->env['post']);
    }

}