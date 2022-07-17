<?php

namespace Modules\Users\Contracts;

use Modules\Users\UserDto;

interface UserWithKeysRepository
{
    public function createUser(int $telegram_id, array $project_ids, string $name);
    public function getUsersByProjectId(int $project_id): array;
    public function isUserExistByTelegramId(int $telegram_id): bool;
    public function getUsersIdByProject(string $project_id);
}