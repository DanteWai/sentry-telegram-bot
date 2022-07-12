<?php

namespace Modules\Bot\Sentry;


use System\DTO;

class WebhookData extends DTO
{
    public string $title;

    public string $event_id;
    public string $project_id;
    public string $issue_id;

    public string $issue_api_url;
    public string $event_web_url;
    public string $event_api_url;

    public string $environment;
}