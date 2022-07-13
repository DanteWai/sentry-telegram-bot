<?php

namespace Modules\Bot\Sentry\TelegramMessageHandlers;

trait MessagesTrait
{
    public function getEmailMessage(): string
    {
        return 'Введите Email (Должен совпадать с вашим Email в sentry)';
    }

    public function getInputCodeMessage(): string
    {
        return 'Введите код из сообщения';
    }

    public function getAuthByKeyMessage(): string
    {
        return 'Введите ключ авторизации';
    }
}