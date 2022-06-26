<?php

namespace Modules\Users;

use System\DTO;

class UserDto extends DTO
{
    public int $telegram_id;
    public int $sentry_id;
    public string $email;

}