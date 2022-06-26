<?php

namespace Modules\Bot\Sentry;


use Database\DatabaseSQLLite;
use GuzzleHttp\Client;
use Modules\Bot\Contracts\WebhookHandlerInterface;
use Modules\Users\Repositories\SqlLiteUserRepository;

class WebhookHandler implements WebhookHandlerInterface
{
    private SentryApi $sentryApi;
    private SqlLiteUserRepository $userRepository;
    private TelegramBot $telegramApi;

    public function __construct()
    {
        $this->sentryApi = new SentryApi();
        $this->userRepository = new SqlLiteUserRepository(new DatabaseSQLLite($_ENV['DATABASE_NAME']));
        $this->telegramApi = new TelegramBot(new Client(), $_ENV['TELEGRAM_SENTRY_BOT_TOKEN']);
    }

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
        ];

        return new WebhookData($webhookData);
    }

    public function handle(array $data)
    {
        $dto = $this->parseData($data);
        $sentry_users = $this->sentryApi->getListAnOrganizationUsers($dto->project_id);
        $users_ids = implode(',', array_map(fn($item) => $item['id'], $sentry_users));

        $users = $this->userRepository->getUsers("where sentry_id in ({$users_ids})");
        $message = "{$dto->title}\n{$dto->event_web_url}";

        foreach ($users as $user){
            $this->telegramApi->sendMessage($user->telegram_id, $message);
        }
    }
}