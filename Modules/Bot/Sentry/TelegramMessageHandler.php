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
use PHPMailer\PHPMailer\Exception;

class TelegramMessageHandler implements TelegramMessageHandlerInterface
{


    private CallbackHandler $callbackHandler;
    private MessageHandler $messageHandler;

    public function __construct(
        CallbackHandler $callbackHandler,
        MessageHandler $messageHandler
    )
    {
        $this->callbackHandler = $callbackHandler;
        $this->messageHandler = $messageHandler;
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function handle(array $data){
        if(isset($data['message'])){
            $this->messageHandler->handle($data['message']);
        } elseif(isset($data['callback_query'])){
            $this->callbackHandler->handle($data['callback_query']);
        }
    }
}