<?php

namespace Modules\Bot\Sentry\TelegramMessageHandlers;

use GuzzleHttp\Exception\GuzzleException;
use Modules\Bot\Sentry\SentryApi;
use Modules\Bot\Sentry\TelegramBot;
use Modules\Projects\SentryProjectService;
use Modules\Users\Contracts\UserCodesRepository;
use Modules\Users\Contracts\UserKeysRepository;
use Modules\Users\Contracts\UserRepository;
use Modules\Users\Contracts\UserWithKeysRepository;
use PHPMailer\PHPMailer\Exception;
use Ramsey\Uuid\Uuid;
use System\Mail;

class MessageHandler
{
    use MessagesTrait;


    private TelegramBot $bot;
    private SentryApi $sentryApi;
    private UserCodesRepository $userCodesRepository;
    private UserRepository $userRepository;
    private UserKeysRepository $userKeysRepository;
    private UserWithKeysRepository $userWithKeysRepository;
    private SentryProjectService $projectService;

    public function __construct(
        TelegramBot            $bot,
        SentryApi              $sentryApi,
        UserCodesRepository    $userCodesRepository,
        UserRepository         $userRepository,
        UserKeysRepository     $userKeysRepository,
        UserWithKeysRepository $userWithKeysRepository,
        SentryProjectService   $projectService
    )
    {
        $this->bot = $bot;
        $this->sentryApi = $sentryApi;
        $this->userCodesRepository = $userCodesRepository;
        $this->userRepository = $userRepository;
        $this->userKeysRepository = $userKeysRepository;
        $this->userWithKeysRepository = $userWithKeysRepository;
        $this->projectService = $projectService;
    }

    /**
     * Выбор варианта обработки входящего сообщения
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
        } elseif ($text === '/create_auth_key_for_project') {
            $this->messageCreateKeyHandler($chat_id);
        } elseif (isset($data['reply_to_message'])) {
            $this->replyToMessageHandler($data);
        }
    }


    /**
     * Обработка сообщения на создание ключа к проекту
     * @param $chat_id
     * @return void
     * @throws GuzzleException
     */
    public function messageCreateKeyHandler($chat_id)
    {
        $admin_ids = str_replace(' ', '', $_ENV['TELEGRAM_SUPER_ADMIN_CHAT_ID'] ?? '');
        $admin_ids = explode(',', $admin_ids);

        if (in_array($chat_id, $admin_ids)) {
            $projects = $this->projectService->getProjects();
            $project_buttons = array_map(function ($item) {
                return [
                    'text' => $item['title'],
                    'callback_data' => 'cb_key_for_' . $item['id']
                ];
            }, $projects);


            $inline_keyboard = [$project_buttons];
            $buttons = ['inline_keyboard' => $inline_keyboard];

            $this->bot->sendMessage($chat_id, 'Выберете проект для которого нужно сгенерировать ключ', $buttons);
        } else {
            $this->bot->sendMessage($chat_id, 'Вы не админ');
        }
    }

    /**
     * Обрабатывает ввод стартовой команды /start
     * @param $chat_id
     * @return void
     * @throws GuzzleException
     */
    protected function messageStartHandler($chat_id)
    {
        $button1 = [
            'text' => 'Авторизация через Email',
            'callback_data' => 'cb_email',
        ];

        $button2 = [
            'text' => 'Авторизация через через ключ',
            'callback_data' => 'cb_key_auth',
        ];

        $inline_keyboard = [[$button1, $button2]];
        $buttons = ['inline_keyboard' => $inline_keyboard];

        $this->bot->sendMessage($chat_id, 'Выберете способ подключения', $buttons);
    }

    /**
     * Обрабатывает ситуации когда пользователь ответил на сообщение бота
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
        } elseif ($data['reply_to_message']['text'] === $this->getAuthByKeyMessage()) {
            $this->replyToAuthKeyMessage($data);
        }
    }

    /**
     * @param $data
     * @return void
     * @throws GuzzleException
     */
    public function replyToAuthKeyMessage($data)
    {
        $chat_id = $data['reply_to_message']['chat']['id'];
        $chat = $data['reply_to_message']['chat'];

        $project_id = $this->userKeysRepository->getProjectIdByKey($data['text']);

        $first_name = $chat['first_name'] ?? '';
        $last_name = $chat['last_name'] ?? '';
        $username = $chat['username'] ?? '';
        $name = str_replace('  ', ' ', "{$username} {$first_name} {$last_name}");


        if ($project_id) {
            $this->userWithKeysRepository->createUser($chat_id, $project_id, $name);
            $project = $this->projectService->getProjectById($project_id);
            $this->bot->sendMessage($chat_id, 'Вы успешно авторизованы для проекта '. $project->title ?? '');
        } else {
            $this->bot->sendMessage($chat_id, 'Авторизация не удалась');
        }
    }

    /**
     * Обрабатывает ввод пользователем емейла
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
     * Обрабатывает ввод пользователем кода, полученного через емейл
     * @param array $data
     * @return void
     * @throws GuzzleException
     */
    protected function replyToInputCodeMessage(array $data)
    {
        $message = $data['text'];
        $chat_id = $data['reply_to_message']['chat']['id'];

        $codeAndEmailAndSentryId = $this->userCodesRepository->getCode($chat_id);

        if (is_array($codeAndEmailAndSentryId) && $codeAndEmailAndSentryId['code'] === $message) {
            $this->userRepository->createUser($chat_id, $codeAndEmailAndSentryId['sentry_id'], $codeAndEmailAndSentryId['email']);
            $this->bot->sendMessage($chat_id, 'Вы успешно авторизованы');
        } else {
            $this->bot->sendMessage($chat_id, 'Введен некорректный код');
        }
    }
}