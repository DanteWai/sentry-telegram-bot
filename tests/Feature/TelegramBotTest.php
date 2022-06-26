<?php

namespace tests\Feature;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Modules\Bot\Sentry\TelegramBot;
use PHPUnit\Framework\TestCase;

class TelegramBotTest extends TestCase
{
    const TEST_MESSAGE = 'test_message';

    public TelegramBot $bot;

    public function setUp(): void
    {
        $this->bot = new TelegramBot(new Client(), $_ENV['TELEGRAM_SENTRY_BOT_TOKEN']);
    }

    /**
     * @throws GuzzleException
     */
    public function testGetBotInfo()
    {
        $answer = $this->bot->getMe();

        $this->assertIsArray($answer);
        $this->assertArrayHasKey('id', $answer);
        $this->assertArrayHasKey('is_bot', $answer);
        $this->assertArrayHasKey('first_name', $answer);
        $this->assertArrayHasKey('username', $answer);
    }

    /**
     * @throws GuzzleException
     */
    public function testSendMessage()
    {
        $this->markTestSkipped('must be revisited.');
        $answer = $this->bot->sendMessage($_ENV['TELEGRAM_TEST_CHAT_ID'], 'test_message');

        $this->assertIsArray($answer);
        $this->assertArrayHasKey('message_id', $answer);


        $this->assertArrayHasKey('from', $answer);
        $this->assertIsArray($answer['from']);
        $this->assertArrayHasKey('id', $answer['from']);
        $this->assertArrayHasKey('is_bot', $answer['from']);
        $this->assertArrayHasKey('first_name', $answer['from']);
        $this->assertArrayHasKey('username', $answer['from']);

        $this->assertArrayHasKey('chat', $answer);
        $this->assertIsArray($answer['chat']);
        $this->assertArrayHasKey('id', $answer['from']);
        $this->assertArrayHasKey('id', $answer['from']);

        $this->assertArrayHasKey('date', $answer);
        $this->assertArrayHasKey('text', $answer);
    }

    /**
     * @throws GuzzleException
     */
    public function testSendButtons()
    {
        $this->markTestSkipped('must be revisited.');
        $button1 = [
            'text' => 'test text',
            'callback_data' => 'cal_data',
            'switch_inline_query_current_chat' => 'switch_inline_query_current_chat'
        ];
        $inline_keyboard = [[$button1]];
        $buttons = ['inline_keyboard' => $inline_keyboard];

        $answer = $this->bot->sendMessage($_ENV['TELEGRAM_TEST_CHAT_ID'], 'test_message', $buttons);

    }

    /**
     * @throws GuzzleException
     */
    public function testSendAction()
    {
        $this->markTestSkipped('must be revisited.');

        $answer = $this->bot->sendChatAction($_ENV['TELEGRAM_TEST_CHAT_ID']);
    }

    /**
     * @throws GuzzleException
     */
    public function testWebhook()
    {
        $this->markTestSkipped('must be revisited.');

        $answer = $this->bot->setWebhook($_ENV['TELEGRAM_WEBHOOK_URL']);

        $this->assertIsArray($answer);
        $this->assertArrayHasKey('ok', $answer);
        $this->assertArrayHasKey('result', $answer);
        $this->assertArrayHasKey('description', $answer);
        $this->assertContains($answer['description'], ['Webhook was set', 'Webhook is already set']);

        $answer = $this->bot->getWebhookInfo();

        $this->assertIsArray($answer);
        $this->assertArrayHasKey('url', $answer);
        $this->assertNotEmpty($answer['url']);


        $answer = $this->bot->deleteWebhook();
        $this->assertIsArray($answer);
        $this->assertContains($answer['description'], ['Webhook was deleted', 'Webhook is already deleted']);
    }

}