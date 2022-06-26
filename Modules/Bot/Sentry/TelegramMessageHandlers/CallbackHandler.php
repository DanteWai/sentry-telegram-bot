<?php

namespace Modules\Bot\Sentry\TelegramMessageHandlers;

use GuzzleHttp\Exception\GuzzleException;
use Modules\Bot\Sentry\TelegramBot;

class CallbackHandler
{
    use MessagesTrait;

    private TelegramBot $bot;

    public function __construct(TelegramBot $bot)
    {
        $this->bot = $bot;
    }

    /**
     * @param array $data
     * @return void
     * @throws GuzzleException
     */
    public function handle(array $data){
        $chat_id = $data['message']['chat']['id'];
        $callback_id = $data['id'];
        $text = $data['data'];

        if($text === 'cb_email'){
            $this->callbackEmailHandler($chat_id, $callback_id);
        }
    }

    /**
     * @param $chat_id
     * @param $callback_id
     * @return void
     * @throws GuzzleException
     */
    protected function callbackEmailHandler($chat_id, $callback_id){
        $this->bot->sendMessage($chat_id, $this->getEmailMessage(), [
            'force_reply' => true
        ]);

        $this->bot->answerCallbackQuery($callback_id);
    }
}