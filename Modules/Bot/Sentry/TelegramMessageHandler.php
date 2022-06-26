<?php

namespace Modules\Bot\Sentry;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Modules\Bot\Contracts\TelegramMessageHandlerInterface;
use Modules\Users\Repositories\RedisUserCodesRepository;
use PHPMailer\PHPMailer\Exception;
use Ramsey\Uuid\Uuid;
use System\Mail;

class TelegramMessageHandler implements TelegramMessageHandlerInterface
{
    const EMAIL_MESSAGE = 'Введите Email (Должен совпадать с вашим Email в sentry)';

    private TelegramBot $bot;
    private SentryApi $sentryApi;
    private RedisUserCodesRepository $codesRepository;

    public function __construct()
    {
        $this->bot = new TelegramBot(new Client(), $_ENV['TELEGRAM_SENTRY_BOT_TOKEN']);
        $this->sentryApi = new SentryApi();
        $this->codesRepository = new RedisUserCodesRepository();
    }

    /**
     * @throws GuzzleException
     */
    public function handle(array $data){
        if(isset($data['message'])){
            $this->messageHandler($data['message']);
        } elseif(isset($data['callback_query'])){
            $this->callbackHandler($data['callback_query']);
        }
    }

    /**
     * @throws GuzzleException
     */
    protected function messageHandler(array $data){
        $chat_id = $data['chat']['id'];
        $text = $data['text'];

        if($text === '/start') {
            $this->messageStartHandler($chat_id);
        } elseif (isset($data['reply_to_message'])){
            $this->replyToMessageHandler($data);
        }
    }

    protected function replyToMessageHandler(array $data){
        if($data['reply_to_message']){
            $this->replyToEmailMessageHandler($data);
        }
    }

    /**
     * @throws Exception
     * @throws GuzzleException
     */
    protected function replyToEmailMessageHandler(array $data){

        $chat_id = $data['reply_to_message']['chat']['id'];

        $email = $data['text'];
        $users = $this->sentryApi->getListAnOrganizationUsers();
        [$user] = array_filter($users, fn($user) => $user['email'] === $email);

        if($user){
            $code = Uuid::uuid4()->toString();
            $message = 'Auth code: ' . $code;
            $this->codesRepository->setCode($chat_id, $code, $email);
            $mail = new Mail($email, $message, 'Auth code');
            $mail->send();
            $this->bot->sendMessage($chat_id, 'Введите код из сообщения', [
                'force_reply' => true
            ]);
        } else {
            $this->bot->sendMessage($chat_id, 'Авторизация не удалась');
        }


    }

    /**
     * @throws GuzzleException
     */
    protected function messageStartHandler($chat_id){
        $button1 = [
            'text' => 'Авторизация через Email',
            'callback_data' => 'cb_email',
        ];

        $inline_keyboard = [[$button1]];
        $buttons = ['inline_keyboard' => $inline_keyboard];

        $this->bot->sendMessage($chat_id, 'Выберете способ подключения', $buttons);
    }

    protected function callbackHandler(array $data){
        $chat_id = $data['message']['chat']['id'];
        $text = $data['data'];

        if($text === 'cb_email'){
            $this->callbackEmailHandler($chat_id);
        }
    }

    protected function callbackEmailHandler($chat_id){
        $this->bot->sendMessage($chat_id, self::EMAIL_MESSAGE, [
            'force_reply' => true
        ]);
    }
}