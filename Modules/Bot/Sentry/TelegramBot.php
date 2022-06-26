<?php

namespace Modules\Bot\Sentry;

use GuzzleHttp\Exception\GuzzleException;
use Modules\Bot\Contracts\TelegramBotAbstract;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class TelegramBot extends TelegramBotAbstract
{
    private Client $client;
    private string $url;

    public function __construct(Client $client, string $bot_token)
    {
        $this->client = $client;
        $this->url = "https://api.telegram.org/bot{$bot_token}/";
    }

    /**
     * @param $chat_id
     * @param WebhookData $webhookData
     * @return void
     */
    public function sendWebhookNotify($chat_id, WebhookData $webhookData)
    {
    }

    /**
     * @return array
     * @throws GuzzleException
     */
    public function getMe(): array
    {
        $response = $this->client->get($this->url . __FUNCTION__);

        return $this->responseHandler($response);
    }

    /**
     * @param $chat_id
     * @param string $message
     * @param array $buttons
     * @return array
     * @throws GuzzleException
     */
    public function sendMessage($chat_id, string $message, array $buttons = []): array
    {
        $data = [
            'chat_id' => $chat_id,
            'text' => $message,
        ];

        if($buttons){
            $data['reply_markup'] = $buttons;
        }

        $response = $this->client->post($this->url . __FUNCTION__, [
            'json' => $data
        ]);

        return $this->responseHandler($response);
    }

    public function sendChatAction($chat_id, string $action = 'typing'): array
    {

        $response = $this->client->post($this->url . __FUNCTION__, [
            'json' => [
                'chat_id' => $chat_id,
                'action' => $action,
            ]
        ]);

        return $this->responseHandler($response);
    }

    /**
     * Метод не нужен если установлен вебхук
     * @param int|null $offset
     * @param int $limit
     * @return array
     * @throws GuzzleException
     */
    public function getUpdates(int $offset = null, int $limit = 100): array
    {
        $response = $this->client->get($this->url . __FUNCTION__, [
            'params' => [
                'offset' => $offset,
                'limit' => $limit
            ]
        ]);

        return $this->responseHandler($response);
    }

    /**
     * @throws GuzzleException
     */
    public function setWebhook($url): array
    {
        $response = $this->client->post($this->url . __FUNCTION__, [
            'json' => [
                'url' => $url,
            ]
        ]);

        return $this->responseHandler($response);
    }

    /**
     * @throws GuzzleException
     */
    public function getWebhookInfo(): array
    {
        $response = $this->client->get($this->url . __FUNCTION__);

        return $this->responseHandler($response);
    }

    /**
     * @throws GuzzleException
     */
    public function deleteWebhook(): array
    {
        $response = $this->client->post($this->url . __FUNCTION__);

        return $this->responseHandler($response);
    }

    protected function responseHandler(ResponseInterface $response): array{
        $response = json_decode($response->getBody(), true);

        if(isset($response['ok']) && $response['ok']){
            if (is_array($response['result'])){
                return  $response['result'];
            }

            return $response;
        }

        $this->wrongAnswer();
    }

    protected function wrongAnswer(){
        throw new \Error('wrong answer');
    }
}