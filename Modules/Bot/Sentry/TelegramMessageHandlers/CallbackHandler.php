<?php

namespace Modules\Bot\Sentry\TelegramMessageHandlers;

use GuzzleHttp\Exception\GuzzleException;
use Modules\Bot\Sentry\TelegramBot;
use Modules\Projects\SentryProjectService;
use Modules\Users\Contracts\UserKeysRepository;
use Ramsey\Uuid\Uuid;

class CallbackHandler
{
    use MessagesTrait;

    private TelegramBot $bot;
    private SentryProjectService $projectService;
    private UserKeysRepository $userKeysRepository;

    public function __construct(
        TelegramBot          $bot,
        SentryProjectService $projectService,
        UserKeysRepository   $userKeysRepository
    )
    {
        $this->bot = $bot;
        $this->projectService = $projectService;
        $this->userKeysRepository = $userKeysRepository;
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
    protected function callbackCreateKeyForProject($chat_id, $callback_id, $text)
    {
        $this->bot->answerCallbackQuery($callback_id);
        $callback_items = explode('_', $text);
        $project_id = array_pop($callback_items);
        $project = $this->projectService->getProjectById($project_id);

        if ($project) {
            $key = Uuid::uuid4()->toString();
            $this->userKeysRepository->addKey($key, $project_id);

            $this->bot->sendMessage($chat_id, "Ключ: {$key}\nДействителен 1 час");
        } else {
            $this->bot->sendMessage($chat_id, 'Проект не найден');
        }
    }
}