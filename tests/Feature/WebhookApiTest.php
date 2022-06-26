<?php

namespace tests\Feature;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class WebhookApiTest extends TestCase
{
    private Client $client;
    private string $base_url;

    protected function setUp(): void
    {
        $this->client = new Client();
        $this->base_url = $_ENV["APP_URL"];
    }

    public function testCheckStatus(){
        $response = $this->client->post($this->getUrl('sentry-webhook'), [
            'json' => ['test' => 'kek']
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    protected function getUrl(string $path):string{
        return str_replace('//', '/', $this->base_url . "/" . $path);
    }
}