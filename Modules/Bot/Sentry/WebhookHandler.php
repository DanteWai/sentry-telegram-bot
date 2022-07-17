<?php

namespace Modules\Bot\Sentry;


use GuzzleHttp\Exception\GuzzleException;
use Modules\Bot\Contracts\WebhookHandlerInterface;
use Modules\Projects\SentryProjectService;
use Modules\Users\Contracts\UserRepository;
use Modules\Users\Contracts\UserWithKeysRepository;

class WebhookHandler implements WebhookHandlerInterface
{
    private SentryApi $sentryApi;
    private UserRepository $userRepository;
    private TelegramBot $telegramApi;
    private SentryProjectService $projectService;
    private UserWithKeysRepository $userWithKeysRepository;

    public function __construct(
        SentryApi            $sentryApi,
        UserRepository       $userRepository,
        TelegramBot          $telegramBot,
        SentryProjectService $projectService,
        UserWithKeysRepository $userWithKeysRepository
    )
    {
        $this->sentryApi = $sentryApi;
        $this->userRepository = $userRepository;
        $this->telegramApi = $telegramBot;
        $this->projectService = $projectService;
        $this->userWithKeysRepository = $userWithKeysRepository;
    }

    /**
     * Парсим данные пришедшие из сентри и забираем необходимое
     * @param array $data
     * @return WebhookData
     */
    public function parseData(array $data): WebhookData
    {
        $data = $data['data']['event'];

        $webhookData = [
            'title' => $data['title'],

            'event_id' => $data['event_id'],
            'project_id' => $data['project'],
            'issue_id' => $data['issue_id'],

            'issue_api_url' => $data['issue_url'],
            'event_web_url' => $data['web_url'],
            'event_api_url' => $data['url'],

            'environment' => $data['environment']
        ];

        return new WebhookData($webhookData);
    }

    /**
     * Получает данные ошибки и рассылает пользователям телеграмм
     *
     * @param array $data
     * @return void
     * @throws GuzzleException
     */
    public function handle(array $data)
    {
        $dto = $this->parseData($data);
        $isProd = $dto->environment === 'prod' || $dto->environment === 'production';

        $project = $this->projectService->getProjectById($dto->project_id);

        $sentry_users = $this->sentryApi->getUsersByProjectId($dto->project_id);
        $users_ids_for_search = array_map(fn($item) => $item['id'], $sentry_users);


        $sentry_users_ids = array_map(fn($item) => $item->telegram_id, $this->userRepository->getUsersBySentryIds($users_ids_for_search));
        $keys_users_ids = $this->userWithKeysRepository->getUsersIdByProject($dto->project_id);


        $message = "Environment: {$dto->environment}\n{$dto->title}\n{$dto->event_web_url}";

        if($project){
            $message = "Project name: {$project->title}\n${message}";
        }

        $this->sentryUsersMessage($sentry_users_ids, $message, $isProd);
        $this->keysUsersMessage($keys_users_ids, $message, $isProd);
    }

    protected function sentryUsersMessage(array $user_ids, string $message, $isProd = false){
        foreach ($user_ids as $telegram_id){
            $this->telegramApi->sendMessage($telegram_id, $message, [], !$isProd);
        }
    }

    protected function keysUsersMessage(array $user_ids, string $message, $isProd = false){
        if($isProd){
            $this->sentryUsersMessage($user_ids, $message, $isProd);
        }
    }
}