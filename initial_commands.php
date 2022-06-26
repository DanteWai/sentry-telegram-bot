<?php
include_once('init.php');

$bot = new \Modules\Bot\Sentry\TelegramBot(
    new \GuzzleHttp\Client(),
    $_ENV['TELEGRAM_SENTRY_BOT_TOKEN']
);

$bot->deleteWebhook();
$bot->setWebhook($_ENV['TELEGRAM_WEBHOOK_URL']);