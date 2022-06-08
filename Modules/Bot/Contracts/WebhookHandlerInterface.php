<?php

namespace Modules\Bot\Contracts;

use System\DTO;

interface WebhookHandlerInterface
{
    public function parseData(array $data): DTO;
}