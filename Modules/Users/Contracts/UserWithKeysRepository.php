<?php

namespace Modules\Users\Contracts;

use Modules\Users\UserDto;

interface UserWithKeysRepository
{
    public function createUser(int $telegram_id, int $project_id, string $name): UserDto;
    public function getUsersByProjectId(int $project_id): array;
}