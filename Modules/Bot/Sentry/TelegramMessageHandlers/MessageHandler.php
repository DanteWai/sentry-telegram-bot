<?php

namespace Modules\Bot\Sentry\TelegramMessageHandlers;

use GuzzleHttp\Exception\GuzzleException;
use Modules\Bot\Sentry\SentryApi;
use Modules\Bot\Sentry\TelegramBot;
use Modules\Users\Contracts\UserCodesRepository;
use Modules\Users\Contracts\UserRepositoryInterface;
use PHPMailer\PHPMailer\Exception;
use Ramsey\Uuid\Uuid;
use System\Mail;

class MessageHandler
{
    use MessagesTrait;


    private TelegramBot $bot;
    private SentryApi $sentryApi;
    private UserCodesRepository $userCodesRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(
        TelegramBot         $bot,
        SentryApi           $sentryApi,
        UserCodesRepository $userCodesRepository,
        UserRepositoryInterface $userRepository
    )
    {
        $this->bot = $bot;
        $this->sentryApi = $sentryApi;
        $this->userCodesRepository = $userCodesRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param array $data
     * @return void
     * @throws GuzzleException
     * @throws Exception
     */
    public function handle(array $data)
    {
        $chat_id = $data['chat']['id'];
        $text = $data['text'];

        if ($text === '/start') {
            $this->messageStartHandler($chat_id);
        } elseif (isset($data['reply_to_message'])) {
            $this->replyToMessageHandler($data);
        }
    }

    /**
     * @throws GuzzleException
     */
    protected function messageStartHandler($chat_id)
    {
        $button1 = [
            'text' => 'Авторизация через Email',
            'callback_data' => 'cb_email',
        ];

        $inline_keyboard = [[$button1]];
        $buttons = ['inline_keyboard' => $inline_keyboard];

        $this->bot->sendMessage($chat_id, 'Выберете способ подключения', $buttons);
    }

    /**
     * @param array $data
     * @return void
     * @throws Exception
     * @throws GuzzleException
     */
    protected function replyToMessageHandler(array $data)
    {
        if ($data['reply_to_message']['text'] === $this->getEmailMessage()) {
            $this->replyToEmailMessageHandler($data);
        } elseif ($data['reply_to_message']['text'] === $this->getInputCodeMessage()) {
            $this->replyToInputCodeMessage($data);
        }
    }

    /**
     * @throws Exception
     * @throws GuzzleException
     */
    protected function replyToEmailMessageHandler(array $data)
    {

        $chat_id = $data['reply_to_message']['chat']['id'];

        $email = $data['text'];
        $users = $this->sentryApi->getListAnOrganizationUsers();
        [$user] = array_filter($users, fn($user) => $user['email'] === $email);

        if ($user) {
            $code = Uuid::uuid4()->toString();
            $message = 'Auth code: ' . $code;
            $sentry_id = $user['id'];

            $this->userCodesRepository->setCode($chat_id, $code, $email, $sentry_id);
            $mail = new Mail($email, $message, 'Auth code');
            $mail->send();
            $this->bot->sendMessage($chat_id, $this->getInputCodeMessage(), [
                'force_reply' => true
            ]);
        } else {
            $this->bot->sendMessage($chat_id, 'Авторизация не удалась');
        }


    }

    /**
     * @param array $data
     * @return void
     * @throws GuzzleException
     */
    protected function replyToInputCodeMessage(array $data){
        $message = $data['text'];
        $chat_id = $data['reply_to_message']['chat']['id'];

        $codeAndEmailAndSentryId = $this->userCodesRepository->getCode($chat_id);

        if(is_array($codeAndEmailAndSentryId) && $codeAndEmailAndSentryId['code'] === $message){
            $this->userRepository->createUser($chat_id,$codeAndEmailAndSentryId['sentry_id'], $codeAndEmailAndSentryId['email']);
            $this->bot->sendMessage($chat_id, 'Вы успешно авторизованы');
        } else {
            $this->bot->sendMessage($chat_id, 'Введен некорректный код');
        }
    }
}