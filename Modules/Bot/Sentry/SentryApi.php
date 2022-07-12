<?php

namespace Modules\Bot\Sentry;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class SentryApi
{

    private $secret;
    private Client $client;

    private $organization_slug;

    public function __construct()
    {
        $this->secret = $_ENV["SENTRY_SECRET"];
        $this->client = new Client([
            'headers' => [
                'Authorization' => "Bearer {$this->secret}",
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'base_uri' => $_ENV['SENTRY_API_URL']
        ]);
        $this->organization_slug = $_ENV['SENTRY_ORGANIZATION_SLUG'];
    }

    /**
     * Получить информацию о владельце токена
     * @throws GuzzleException
     */
    public function getMe(): array
    {
        $response = $this->client->get('');

        return $this->responseHandler($response);
    }

    /**
     * Получить список пользователей организации
     * Позволяет ограничить пользователей по проекту
     * @throws GuzzleException
     */
    public function getListAnOrganizationUsers(int $project_id = null): array
    {
        $path = "organizations/{$this->organization_slug}/users/";

        if($project_id){
            $path .= "?project={$project_id}";
        }

        $response = $this->client->get($path);

        return $this->responseHandler($response);
    }

    /**
     * @return array
     * @throws GuzzleException
     */
    public function getOrganizationProjects(): array
    {
        $path = "organizations/{$this->organization_slug}/projects/";

        $response = $this->client->get($path);

        return $this->responseHandler($response);
    }

    /**
     * @throws GuzzleException
     */
    public function getUsersByProjectId(int $project_id): array
    {
        return $this->getListAnOrganizationUsers($project_id);
    }

    /**
     * @throws GuzzleException
     */
    public function getProjectBySlug($project_slug): array
    {
        $response = $this->client->get("projects/{$this->organization_slug}/{$project_slug}/");

        return $this->responseHandler($response);
    }

    protected function responseHandler(ResponseInterface $response): array{
        return json_decode($response->getBody(), true);
    }

}