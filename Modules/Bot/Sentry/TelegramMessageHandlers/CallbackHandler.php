<?php

namespace Modules\Bot\Sentry\TelegramMessageHandlers;

use GuzzleHttp\Exception\GuzzleException;
use Modules\Bot\Sentry\TelegramBot;
use Modules\Projects\SentryProjectService;
use Modules\Users\Contracts\UserKeysRepository;
use Modules\Users\Contracts\UserWithKeysRepository;
use Ramsey\Uuid\Uuid;

class CallbackHandler
{
    use MessagesTrait, ButtonsTrait;

    private TelegramBot $bot;
    private SentryProjectService $projectService;
    private UserKeysRepository $userKeysRepository;
    private UserWithKeysRepository $userWithKeysRepository;

    public function __construct(
        TelegramBot          $bot,
        SentryProjectService $projectService,
        UserKeysRepository   $userKeysRepository,
        UserWithKeysRepository   $userWithKeysRepository
    )
    {
        $this->bot = $bot;
        $this->projectService = $projectService;
        $this->userKeysRepository = $userKeysRepository;
        $this->userWithKeysRepository = $userWithKeysRepository;
    }

    /**
     * @param array $data
     * @return void
     * @throws GuzzleException
     */
    public function handle(array $data)
    {
        $chat_id = $data['message']['chat']['id'];
        $callback_id = $data['id'];
        $text = $data['data'];

        if ($text === 'cb_email') {
            $this->callbackEmailHandler($chat_id, $callback_id);
        } elseif ($text === 'cb_key_auth') {
            $this->authByKeyHandler($chat_id, $callback_id);
        } elseif (substr($text, 0, 11) === "cb_key_for_") {
            $this->callbackAddProjectToCreatingKey($chat_id, $callback_id, $text);
        } elseif (substr($text, 0, 14) === "cb_delete_key_") {
            $this->callbackDeleteKey($chat_id, $callback_id, $text);
        } elseif ($text === 'cb_key_generate') {
            $this->callbackCreateKeyForProject($chat_id, $callback_id, $text);
        }
    }

    /**
     * @param $chat_id
     * @param $callback_id
     * @return void
     * @throws GuzzleException
     */
    protected function authByKeyHandler($chat_id, $callback_id)
    {
        $this->bot->sendMessage($chat_id, $this->getAuthByKeyMessage(), [
            'force_reply' => true
        ]);

        $this->bot->answerCallbackQuery($callback_id);
    }

    /**
     * @param $chat_id
     * @param $callback_id
     * @return void
     * @throws GuzzleException
     */
    protected function callbackEmailHandler($chat_id, $callback_id)
    {
        $this->bot->sendMessage($chat_id, $this->getEmailMessage(), [
            'force_reply' => true
        ]);

        $this->bot->answerCallbackQuery($callback_id);
    }

    /**
     * @param $chat_id
     * @param $callback_id
     * @param $text
     * @return void
     * @throws GuzzleException
     */
    protected function callbackAddProjectToCreatingKey($chat_id, $callback_id, $text)
    {
        $this->bot->answerCallbackQuery($callback_id);
        $callback_items = explode('_', $text);
        $project_id = array_pop($callback_items);
        $project = $this->projectService->getProjectById($project_id);

        if ($project) {
            $this->userKeysRepository->addProjectToCreatingKey($chat_id, $project_id);

            $buttons = $this->getCallbackAddProjectToCreatingKeyButtons($chat_id);
            $this->bot->sendMessage($chat_id, "Выберете следующий проект или сгенерируйте ключ", $buttons);
        } else {
            $this->bot->sendMessage($chat_id, 'Проект не найден');
        }
    }

    /**
     * @param $chat_id
     * @param $callback_id
     * @param $text
     * @return void
     * @throws GuzzleException
     */
    protected function callbackDeleteKey($chat_id, $callback_id, $text){
        $this->bot->answerCallbackQuery($callback_id);
        $callback_items = explode('_', $text);
        $telegram_id = array_pop($callback_items);

        $this->userWithKeysRepository->deleteUser($telegram_id);

        $this->bot->sendMessage($chat_id, 'Ключ удален');
    }

    /**
     * @param $chat_id
     * @return array[]
     * @throws GuzzleException
     */
    protected function getCallbackAddProjectToCreatingKeyButtons($chat_id): array
    {
        $project_buttons = $this->getButtonsForProjects(
            $this->projectService->getProjects(),
            $this->userKeysRepository->getProjectsForCreatingKey($chat_id)
        );

        $inline_keyboard = [$project_buttons, [
            [
                'text' => 'Сгенерировать',
                'callback_data' => 'cb_key_generate'
            ]
        ]];

        return ['inline_keyboard' => $inline_keyboard];
    }

    /**
     * @param $chat_id
     * @param $callback_id
     * @param $text
     * @return void
     * @throws GuzzleException
     */
    protected function callbackCreateKeyForProject($chat_id, $callback_id, $text)
    {
        $this->bot->answerCallbackQuery($callback_id);
        $key = Uuid::uuid4()->toString();
        $project_ids = implode(',',
            array_unique(
                $this->userKeysRepository->getProjectsForCreatingKey($chat_id)
            )
        );

        $this->userKeysRepository->addKey($key, $project_ids);
        $this->bot->sendMessage($chat_id, "Ключ: {$key}\nДействителен 1 час");
        $this->userKeysRepository->startCreatingNewKey($chat_id);
    }
}