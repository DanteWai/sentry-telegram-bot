<?php

namespace Modules\Bot\Sentry;

use Database\DatabaseSQLLite;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Modules\Bot\Contracts\TelegramMessageHandlerInterface;
use Modules\Bot\Sentry\TelegramMessageHandlers\CallbackHandler;
use Modules\Bot\Sentry\TelegramMessageHandlers\MessageHandler;
use Modules\Users\Contracts\UserRepositoryInterface;
use Modules\Users\Repositories\RedisUserCodesRepository;
use Modules\Users\Repositories\SqlLiteUserRepository;

class TelegramMessageHandler implements TelegramMessageHandlerInterface
{


    private TelegramBot $telegramApi;
    private SentryApi $sentryApi;
    private RedisUserCodesRepository $codesRepository;
    private CallbackHandler $callbackHandler;
    private MessageHandler $messageHandler;
    private UserRepositoryInterface $usersRepository;

    public function __construct()
    {
        $this->telegramApi = new TelegramBot(new Client(), $_ENV['TELEGRAM_SENTRY_BOT_TOKEN']);
        $this->sentryApi = new SentryApi();

        $this->codesRepository = new RedisUserCodesRepository();
        $this->usersRepository = new SqlLiteUserRepository(new DatabaseSQLLite($_ENV['DATABASE_NAME']));

        $this->callbackHandler = new CallbackHandler($this->telegramApi);
        $this->messageHandler = new MessageHandler(
            $this->telegramApi,
            $this->sentryApi,
            $this->codesRepository,
            $this->usersRepository
        );
    }

    /**
     * @throws GuzzleException
     */
    public function handle(array $data){
        if(isset($data['message'])){
            $this->messageHandler->handle($data['message']);
        } elseif(isset($data['callback_query'])){
            $this->callbackHandler->handle($data['callback_query']);
        }
    }
}