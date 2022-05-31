<?php

namespace Modules\Bot;

use Modules\Contracts\WebhookHandlerInterface;
use System\BaseController;

class WebHookController extends BaseController
{
    public function __construct()
    {
    }

    public function sentry(WebhookHandlerInterface $handler){
    }

}