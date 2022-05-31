<?php
return [
    \Modules\Bot\WebHookController::class => DI\create()
        ->method('sentry', DI\create(\Modules\Bot\Sentry\WebhookHandler::class)),
];