<?php

namespace Modules\Bot\Sentry;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class SentryApi
{
    /**
     * @var mixed
     */
    private $secret;
    private Client $client;
    private string $url;

    public function __construct()
    {
        $this->secret = $_ENV["SENTRY_SECRET"];
        $this->client = new Client([
            'headers' => [
                'Authorization' => "Bearer {$this->secret}",
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'base_uri' => 'https://sentry.io/api/0/'
        ]);
        $this->organization_slug = $_ENV['SENTRY_ORGANIZATION_SLUG'];
    }

    /**
     * @throws GuzzleException
     */
    public function getMe(): array
    {
        $response = $this->client->get('');

        return $this->responseHandler($response);
    }

    /**
     * @throws GuzzleException
     */
    public function getListAnOrganizationUsers(): array
    {
        $response = $this->client->get("organizations/dim-ur/users/");

        return $this->responseHandler($response);
    }

    protected function responseHandler(ResponseInterface $response): array{
        return json_decode($response->getBody(), true);
    }

}