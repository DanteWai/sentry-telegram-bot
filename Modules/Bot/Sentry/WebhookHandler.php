<?php

namespace Modules\Bot\Sentry;


use GuzzleHttp\Exception\GuzzleException;
use Modules\Bot\Contracts\WebhookHandlerInterface;
use Modules\Projects\SentryProjectService;
use Modules\Users\Contracts\UserRepositoryInterface;

class WebhookHandler implements WebhookHandlerInterface
{
    private SentryApi $sentryApi;
    private UserRepositoryInterface $userRepository;
    private TelegramBot $telegramApi;
    private SentryProjectService $projectService;

    public function __construct(
        SentryApi $sentryApi,
        UserRepositoryInterface $userRepository,
        TelegramBot $telegramBot,
        SentryProjectService $projectService
    )
    {
        $this->sentryApi = $sentryApi;
        $this->userRepository = $userRepository;
        $this->telegramApi = $telegramBot;
        $this->projectService = $projectService;
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

        $project = $this->projectService->getProjectById($dto->project_id);

        $sentry_users = $this->sentryApi->getUsersByProjectId($dto->project_id);
        $users_ids = array_map(fn($item) => $item['id'], $sentry_users);
        $users = $this->userRepository->getUsersBySentryIds($users_ids);

        $message = "Environment: {$dto->environment}\n{$dto->title}\n{$dto->event_web_url}";

        if($project){
            $message = "Project name: {$project->title}\n${message}";
        }

        foreach ($users as $user){
            $this->telegramApi->sendMessage($user->telegram_id, $message);
        }
    }
}