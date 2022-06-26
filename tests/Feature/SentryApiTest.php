<?php

namespace tests\Feature;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Modules\Bot\Sentry\SentryApi;
use Modules\Bot\Sentry\TelegramBot;
use PHPUnit\Framework\TestCase;

class SentryApiTest extends TestCase
{
    public SentryApi $api;

    public function setUp(): void
    {
        $this->api = new SentryApi();
    }

    /**
     * @throws GuzzleException
     */
    public function testGetAppInfo()
    {
        $this->markTestSkipped('must be revisited.');
        $answer = $this->api->getMe();
        $this->assertIsArray($answer);
        $this->assertArrayHasKey('user', $answer);
    }

    /**
     * @throws GuzzleException
     */
    public function testOrganizationMembers()
    {
        $answer = $this->api->getListAnOrganizationUsers();
        $this->assertIsArray($answer);
    }
}