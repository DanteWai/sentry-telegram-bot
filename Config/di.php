<?php


use function DI\create;
use function DI\get;

return [
    \Modules\Bot\Contracts\TelegramMessageHandlerInterface::class =>
        create(\Modules\Bot\Sentry\TelegramMessageHandler::class),

    \Modules\Bot\WebHookController::class => create()->constructor(
        //get(\Modules\Bot\Contracts\WebhookHandlerInterface::class),
        create(\Modules\Bot\Contracts\TelegramMessageHandlerInterface::class),
    ),



//    \Modules\Bot\Contracts\WebhookHandlerInterface::class =>
//        DI\create(\Modules\Bot\Sentry\WebhookHandler::class)->constructor(
//            get('SentryApi'),
//            get('UserRepository'),
//            get('TelegramApi'),
//        ),
//
//
//    \Modules\Bot\Contracts\TelegramMessageHandlerInterface::class =>
//        DI\create(\Modules\Bot\Sentry\TelegramMessageHandler::class),
//
//
//    'UserRepository' => DI\create(\Modules\Users\Repositories\SqlLiteUserRepository::class)->constructor(
//        DI\create(\Database\DatabaseSQLLite::class)->constructor(
//            $_ENV['DATABASE_NAME']
//        ),
//    ),
//
//
//    'SentryApi' => create(\Modules\Bot\Sentry\SentryApi::class),
//    'TelegramApi' => create(\Modules\Bot\Sentry\TelegramBot::class)->constructor(
//        [
//            DI\create(\GuzzleHttp\Client::class),
//            $_ENV['TELEGRAM_SENTRY_BOT_TOKEN']
//        ]
//    ),
];