<?php

namespace Modules\Bot\Contracts;

interface TelegramMessageHandlerInterface
{
    public function handle(array $data);
}