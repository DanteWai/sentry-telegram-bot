<?php

return [
    \Modules\Bot\WebHookController::class => DI\autowire()
        ->constructor(
            DI\autowire(\Modules\Bot\Sentry\WebhookHandler::class),
            DI\autowire(\Modules\Bot\Sentry\TelegramMessageHandler::class)
        ),
];