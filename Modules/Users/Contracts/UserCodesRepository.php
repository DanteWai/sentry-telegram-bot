<?php

namespace Modules\Users\Contracts;

interface UserCodesRepository
{
    public function setCode(int $user_id, string $code, string $email, string $sentry_id): void;

    public function getCode($user_id): ?array;
}