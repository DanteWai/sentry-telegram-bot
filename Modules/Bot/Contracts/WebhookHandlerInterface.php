<?php

namespace Modules\Contracts;

use System\DTO;

interface WebhookHandlerInterface
{
    public function parseData(array $data): DTO;
}