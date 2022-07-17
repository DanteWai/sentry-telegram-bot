<?php

namespace Modules\Users\Contracts;

interface UserWithKeysRepository
{
    public function createUser(int $telegram_id, array $project_ids, string $name);
    public function getUsersWithKey(): array;
    public function isUserExistByTelegramId(int $telegram_id): bool;
    public function getUsersIdByProject(string $project_id);
    public function deleteUser(int $telegram_id);
}