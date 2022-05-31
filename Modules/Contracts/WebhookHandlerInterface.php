<?php

namespace Modules\Contracts;

interface WebhookHandlerInterface
{
    public function parseData(array $data): WebhookDataDtoInterface;
}